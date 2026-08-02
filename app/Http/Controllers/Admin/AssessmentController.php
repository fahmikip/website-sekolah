<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAssessmentRequest;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Classroom;
use App\Models\LearningObjective;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function index()
    {
        return view('admin.assessment.manage-index', ['assessments' => Assessment::with(['classroom', 'subject', 'teacher'])->withCount(['components', 'studentScores'])->latest('assessment_date')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.assessment.manage-form', $this->options());
    }

    public function edit(Assessment $assessment)
    {
        return view('admin.assessment.manage-form', $this->options() + ['assessment' => $assessment->load('components')]);
    }

    public function store(SaveAssessmentRequest $request)
    {
        $assessment = DB::transaction(function () use ($request) {
            $data = $request->safe()->except('components');
            $assessment = Assessment::create($data);
            $assessment->components()->createMany($request->validated('components'));
            Classroom::with('students')->findOrFail($data['classroom_id'])->students->each(fn ($student) => $assessment->studentScores()->firstOrCreate(['student_id' => $student->id]));

            return $assessment;
        });

        return redirect()->route('admin.assessments.edit', $assessment)->with('success', 'Asesmen dan peserta berhasil dibuat.');
    }

    public function update(SaveAssessmentRequest $request, Assessment $assessment)
    {
        DB::transaction(function () use ($request, $assessment) {
            $assessment->update($request->safe()->except('components'));
            foreach ($request->validated('components') as $index => $component) {
                $assessment->components()->updateOrCreate(['id' => $assessment->components()->skip($index)->value('id')], $component);
            }
        });

        return back()->with('success', 'Asesmen berhasil diperbarui.');
    }

    public function destroy(Assessment $assessment)
    {
        abort_if($assessment->studentScores()->whereNotNull('final_score')->exists(), 422, 'Asesmen bernilai tidak dapat dihapus.');
        $assessment->delete();

        return back()->with('success', 'Asesmen dihapus.');
    }

    private function options(): array
    {
        return ['academicYears' => AcademicYear::orderByDesc('starts_on')->get(), 'semesters' => Semester::all(), 'classrooms' => Classroom::orderBy('name')->get(), 'subjects' => Subject::orderBy('name')->get(), 'teachers' => Teacher::orderBy('name')->get(), 'types' => AssessmentType::orderBy('name')->get(), 'objectives' => LearningObjective::orderBy('sequence')->get()];
    }
}
