<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssessmentScoresExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportAssessmentScoresRequest;
use App\Http\Requests\SaveScoresRequest;
use App\Imports\AssessmentScoresImport;
use App\Models\AchievementCriterion;
use App\Models\Assessment;
use App\Models\StudentScore;
use App\Services\ScorePersistenceService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScoreEntryController extends Controller
{
    public function index()
    {
        return view('admin.assessment.scores-index', ['assessments' => Assessment::withCount('studentScores')->latest('assessment_date')->paginate(15)]);
    }

    public function edit(Assessment $assessment)
    {
        $assessment->load('components');
        $scores = $assessment->studentScores()->with(['student', 'details'])->get();

        return view('admin.assessment.scores-edit', compact('assessment', 'scores'));
    }

    public function update(SaveScoresRequest $request, Assessment $assessment, ScorePersistenceService $service)
    {
        foreach ($request->validated('scores') as $row) {
            $service->save(StudentScore::findOrFail($row['student_score_id']), $row['details'], $request->user(), $row['reason']);
        }

        return back()->with('success', 'Nilai berhasil disimpan dan diaudit.');
    }

    public function autosave(SaveScoresRequest $request, Assessment $assessment, ScorePersistenceService $service)
    {
        $row = $request->validated('scores')[0];
        $score = StudentScore::where('assessment_id', $assessment->id)->findOrFail($row['student_score_id']);
        $saved = $service->save($score, $row['details'], $request->user(), $row['reason']);

        return response()->json(['saved' => true, 'final_score' => $saved->final_score, 'saved_at' => now()->toIso8601String()]);
    }

    public function export(Assessment $assessment)
    {
        $assessment->load(['components', 'studentScores.student', 'studentScores.details']);

        return Excel::download(new AssessmentScoresExport($assessment), 'nilai-'.$assessment->id.'.xlsx');
    }

    public function import(ImportAssessmentScoresRequest $request, Assessment $assessment)
    {
        $import = new AssessmentScoresImport($assessment, $request->user());
        Excel::import($import, $request->file('file'));

        return back()->with('success', "{$import->count} nilai berhasil diimpor dan diaudit.");
    }

    public function exportAnalysis(Assessment $assessment)
    {
        return $this->export($assessment);
    }

    public function transition(Request $request, StudentScore $studentScore)
    {
        $request->validate(['status' => 'required|in:submitted,verified,locked']);
        $permission = match ($request->status) {
            'submitted' => 'input_scores','verified' => 'verify_scores','locked' => 'lock_scores'
        };
        abort_unless($request->user()->can($permission), 403);
        $data = ['status' => $request->status];
        if ($request->status === 'submitted') {
            $data += ['submitted_by' => $request->user()->id, 'submitted_at' => now()];
        }if ($request->status === 'verified') {
            $data += ['verified_by' => $request->user()->id, 'verified_at' => now()];
        }if ($request->status === 'locked') {
            $data['locked_at'] = now();
        }$studentScore->update($data);

        return back()->with('success', 'Status nilai diperbarui.');
    }

    public function analysis(Assessment $assessment)
    {
        $scores = $assessment->studentScores()->with(['student', 'details.component'])->whereNotNull('final_score')->get();
        $values = $scores->pluck('final_score')->sort()->values();
        $passing = AchievementCriterion::where('subject_id', $assessment->subject_id)->where('is_active', true)->value('passing_score') ?? 75;
        $stats = ['average' => round($values->avg() ?? 0, 2), 'median' => $values->median() ?? 0, 'highest' => $values->max() ?? 0, 'lowest' => $values->min() ?? 0, 'passed' => $values->filter(fn ($v) => $v >= $passing)->count(), 'needs_remedial' => $values->filter(fn ($v) => $v < $passing)->count(), 'passing' => $passing];

        return view('admin.assessment.analysis', compact('assessment', 'scores', 'stats'));
    }
}
