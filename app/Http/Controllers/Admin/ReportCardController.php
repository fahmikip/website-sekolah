<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Services\ReportCardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index()
    {
        return view('admin.reports.index', ['reports' => ReportCard::with(['student', 'classroom', 'semester'])->latest()->paginate(20), 'classrooms' => Classroom::orderBy('name')->get()]);
    }

    public function generate(Request $request, ReportCardService $service)
    {
        $data = $request->validate(['classroom_id' => 'required|exists:classrooms,id']);
        $classroom = Classroom::with('students')->findOrFail($data['classroom_id']);
        $year = AcademicYear::where('is_active', true)->firstOrFail();
        $semester = Semester::where('academic_year_id', $year->id)->where('is_active', true)->firstOrFail();
        foreach ($classroom->students as $student) {
            $service->generate($student, $year->id, $semester->id, $classroom->id);
        }

        return back()->with('success', 'Rapor kelas berhasil dihitung ulang.');
    }

    public function show(ReportCard $reportCard)
    {
        $reportCard->load(['student', 'classroom', 'academicYear', 'semester', 'scores.subject']);

        return view('reports.show', ['report' => $reportCard, 'print' => false]);
    }

    public function update(Request $request, ReportCard $reportCard)
    {
        abort_if($reportCard->locked_at && ! $request->user()->can('lock_report_cards'), 403);
        $data = $request->validate([
            'homeroom_note' => 'nullable|string|max:2000', 'promotion_decision' => 'nullable|string|max:100',
            'sick_days' => 'integer|min:0|max:365', 'excused_days' => 'integer|min:0|max:365', 'unexcused_days' => 'integer|min:0|max:365',
            'descriptions' => 'array', 'descriptions.*' => 'nullable|string|max:2000',
        ]);
        $reportCard->update(collect($data)->except('descriptions')->all());
        foreach ($data['descriptions'] ?? [] as $id => $description) {
            $reportCard->scores()->whereKey($id)->update(['description' => $description, 'description_approved' => true]);
        }

        return back()->with('success', 'Rapor berhasil diperbarui.');
    }

    public function transition(Request $request, ReportCard $reportCard)
    {
        $data = $request->validate(['status' => 'required|in:draft,verified,published,locked']);
        $attributes = ['status' => $data['status']];
        if ($data['status'] === 'verified') {
            $attributes += ['verified_by' => $request->user()->id, 'verified_at' => now()];
        }
        if ($data['status'] === 'published') {
            $attributes['published_at'] = now();
        }
        if ($data['status'] === 'locked') {
            $attributes['locked_at'] = now();
        }
        $reportCard->update($attributes);

        return back()->with('success', 'Status rapor diperbarui.');
    }

    public function print(ReportCard $reportCard)
    {
        $reportCard->increment('print_count');
        $reportCard->update(['last_printed_at' => now()]);

        return view('reports.show', ['report' => $reportCard->load(['student', 'classroom', 'academicYear', 'semester', 'scores.subject']), 'print' => true]);
    }

    public function pdf(ReportCard $reportCard)
    {
        $reportCard->increment('print_count');
        $reportCard->update(['last_printed_at' => now()]);
        $report = $reportCard->load(['student', 'classroom', 'academicYear', 'semester', 'scores.subject']);

        return Pdf::loadView('reports.pdf', compact('report'))->setPaper('a4')->download('rapor-'.$report->student->nis.'.pdf');
    }
}
