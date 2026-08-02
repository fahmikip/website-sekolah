<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAssessmentConfigurationRequest;
use App\Models\AcademicYear;
use App\Models\AchievementCriterion;
use App\Models\AssessmentType;
use App\Models\AssessmentWeight;
use App\Models\Classroom;
use App\Models\CriterionRange;
use App\Models\Curriculum;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\LearningScope;
use App\Models\Level;
use App\Models\Phase;
use App\Models\Semester;
use App\Models\Subject;

class AssessmentConfigurationController extends Controller
{
    private const MODULES = ['outcomes' => [LearningOutcome::class, 'Capaian Pembelajaran', 'code'], 'objectives' => [LearningObjective::class, 'Tujuan Pembelajaran', 'code'], 'scopes' => [LearningScope::class, 'Lingkup Materi', 'title'], 'criteria' => [AchievementCriterion::class, 'KKTP', 'name'], 'ranges' => [CriterionRange::class, 'Rentang Kriteria', 'label'], 'types' => [AssessmentType::class, 'Jenis Asesmen', 'name'], 'weights' => [AssessmentWeight::class, 'Bobot Nilai', 'weight']];

    public function index(string $module)
    {
        [$model,$label,$title] = $this->config($module);

        return view('admin.assessment.config-index', ['module' => $module, 'label' => $label, 'title' => $title, 'items' => $model::latest()->paginate(15)]);
    }

    public function create(string $module)
    {
        [, $label] = $this->config($module);

        return view('admin.assessment.config-form', [...$this->options(), 'module' => $module, 'label' => $label]);
    }

    public function store(SaveAssessmentConfigurationRequest $request, string $module)
    {
        [$model] = $this->config($module);
        $model::create($request->validated());

        return redirect()->route('admin.assessment.config.index', $module)->with('success', 'Konfigurasi disimpan.');
    }

    public function edit(string $module, int $id)
    {
        [$model,$label] = $this->config($module);

        return view('admin.assessment.config-form', [...$this->options(), 'module' => $module, 'label' => $label, 'item' => $model::findOrFail($id)]);
    }

    public function update(SaveAssessmentConfigurationRequest $request, string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->update($request->validated());

        return redirect()->route('admin.assessment.config.index', $module)->with('success', 'Konfigurasi diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Konfigurasi dihapus.');
    }

    private function config(string $module): array
    {
        abort_unless(isset(self::MODULES[$module]), 404);

        return self::MODULES[$module];
    }

    private function options(): array
    {
        return ['curricula' => Curriculum::all(), 'phases' => Phase::all(), 'subjects' => Subject::all(), 'academicYears' => AcademicYear::all(), 'outcomes' => LearningOutcome::all(), 'objectives' => LearningObjective::all(), 'criteria' => AchievementCriterion::all(), 'levels' => Level::all(), 'classrooms' => Classroom::all(), 'semesters' => Semester::all(), 'types' => AssessmentType::all()];
    }
}
