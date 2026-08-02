<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Enrichment;
use App\Models\Remedial;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function index(string $module)
    {
        [$model, $label] = $this->config($module);

        return view('admin.assessment.interventions-index', ['module' => $module, 'label' => $label, 'items' => $model::with($module === 'remedials' ? ['student', 'assessment'] : ['student', 'subject'])->latest()->paginate(20)]);
    }

    public function create(string $module)
    {
        [, $label] = $this->config($module);

        return view('admin.assessment.interventions-form', $this->viewData($module, $label));
    }

    public function store(Request $request, string $module)
    {
        [$model] = $this->config($module);
        $model::create($this->validateData($request, $module));

        return redirect()->route('admin.assessment.interventions.index', $module)->with('success', 'Data intervensi disimpan.');
    }

    public function edit(string $module, int $id)
    {
        [$model, $label] = $this->config($module);

        return view('admin.assessment.interventions-form', $this->viewData($module, $label) + ['item' => $model::findOrFail($id)]);
    }

    public function update(Request $request, string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->update($this->validateData($request, $module));

        return redirect()->route('admin.assessment.interventions.index', $module)->with('success', 'Data intervensi diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Data intervensi dihapus.');
    }

    private function config(string $module): array
    {
        abort_unless(in_array($module, ['remedials', 'enrichments']), 404);

        return $module === 'remedials' ? [Remedial::class, 'Remedial'] : [Enrichment::class, 'Pengayaan'];
    }

    private function viewData(string $module, string $label): array
    {
        return ['module' => $module, 'label' => $label, 'students' => Student::orderBy('name')->get(), 'assessments' => Assessment::orderByDesc('assessment_date')->get(), 'subjects' => Subject::orderBy('name')->get()];
    }

    private function validateData(Request $request, string $module): array
    {
        abort_unless($request->user()->can('manage_assessment'), 403);

        return $module === 'remedials'
            ? $request->validate(['student_id' => 'required|exists:students,id', 'assessment_id' => 'required|exists:assessments,id', 'old_score' => 'required|numeric|between:0,100', 'remedial_score' => 'nullable|numeric|between:0,100', 'final_score' => 'nullable|numeric|between:0,100', 'remedial_type' => 'nullable|string|max:100', 'date' => 'nullable|date', 'teacher_note' => 'nullable|string|max:2000'])
            : $request->validate(['student_id' => 'required|exists:students,id', 'subject_id' => 'required|exists:subjects,id', 'activity' => 'required|string|max:255', 'description' => 'nullable|string|max:2000', 'result' => 'nullable|string|max:2000', 'teacher_note' => 'nullable|string|max:2000']);
    }
}
