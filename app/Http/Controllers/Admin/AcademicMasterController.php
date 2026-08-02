<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAcademicMasterRequest;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Curriculum;
use App\Models\Level;
use App\Models\ParentProfile;
use App\Models\Phase;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Validation\ValidationException;

class AcademicMasterController extends Controller
{
    private const MODULES = ['academic-years' => [AcademicYear::class, 'Tahun Ajaran', 'name'], 'semesters' => [Semester::class, 'Semester', 'name'], 'curricula' => [Curriculum::class, 'Kurikulum', 'name'], 'phases' => [Phase::class, 'Fase', 'name'], 'levels' => [Level::class, 'Jenjang', 'name'], 'classrooms' => [Classroom::class, 'Rombel', 'name'], 'subjects' => [Subject::class, 'Mata Pelajaran', 'name'], 'teachers' => [Teacher::class, 'Guru', 'name'], 'students' => [Student::class, 'Siswa', 'name'], 'parents' => [ParentProfile::class, 'Orang Tua', 'name'], 'schedules' => [Schedule::class, 'Jadwal', 'room']];

    public function index(string $module)
    {
        [$model,$label,$title] = $this->config($module);
        $items = $model::query()->when(request('search'), fn ($q, $s) => $q->where($title, 'like', "%{$s}%"))->latest()->paginate(15)->withQueryString();

        return view('admin.academic.index', compact('module', 'label', 'title', 'items'));
    }

    public function create(string $module)
    {
        [, $label] = $this->config($module);

        return view('admin.academic.form', [...$this->options(), 'module' => $module, 'label' => $label]);
    }

    public function edit(string $module, int $id)
    {
        [$model,$label] = $this->config($module);

        return view('admin.academic.form', [...$this->options(), 'module' => $module, 'label' => $label, 'item' => $model::findOrFail($id)]);
    }

    public function store(SaveAcademicMasterRequest $request, string $module)
    {
        [$model] = $this->config($module);
        $data = $request->validated();
        if ($module === 'schedules') {
            $this->ensureNoConflict($data);
        }$model::create($data);

        return redirect()->route('admin.academic.index', $module)->with('success', 'Data akademik ditambahkan.');
    }

    public function update(SaveAcademicMasterRequest $request, string $module, int $id)
    {
        [$model] = $this->config($module);
        $item = $model::findOrFail($id);
        $data = $request->validated();
        if ($module === 'schedules') {
            $this->ensureNoConflict($data, $id);
        }$item->update($data);

        return redirect()->route('admin.academic.index', $module)->with('success', 'Data akademik diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Data dihapus.');
    }

    private function config(string $module): array
    {
        abort_unless(isset(self::MODULES[$module]), 404);

        return self::MODULES[$module];
    }

    private function options(): array
    {
        return ['academicYears' => AcademicYear::orderByDesc('starts_on')->get(), 'semesters' => Semester::all(), 'curricula' => Curriculum::all(), 'phases' => Phase::all(), 'levels' => Level::orderBy('sort_order')->get(), 'classrooms' => Classroom::all(), 'subjects' => Subject::all(), 'teachers' => Teacher::where('status', 'active')->get()];
    }

    private function ensureNoConflict(array $data, ?int $ignore = null): void
    {
        $overlap = fn ($q) => $q->where('starts_at', '<', $data['ends_at'])->where('ends_at', '>', $data['starts_at']);
        $base = Schedule::where('academic_year_id', $data['academic_year_id'])->where('semester_id', $data['semester_id'])->where('day_of_week', $data['day_of_week'])->when($ignore, fn ($q) => $q->whereKeyNot($ignore));
        $teacher = (clone $base)->where('teacher_id', $data['teacher_id'])->where($overlap)->exists();
        $class = (clone $base)->where('classroom_id', $data['classroom_id'])->where($overlap)->exists();
        $room = $data['room'] && (clone $base)->where('room', $data['room'])->where($overlap)->exists();
        if ($teacher || $class || $room) {
            throw ValidationException::withMessages(['starts_at' => 'Jadwal bentrok dengan guru, kelas, atau ruangan pada waktu yang sama.']);
        }
    }
}
