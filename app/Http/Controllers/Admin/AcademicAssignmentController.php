<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAcademicAssignmentRequest;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class AcademicAssignmentController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->can('edit_academic'), 403);

        return view('admin.academic.assignments', ['academicYears' => AcademicYear::orderByDesc('starts_on')->get(), 'classrooms' => Classroom::withCount('students')->get(), 'students' => Student::where('status', 'active')->orderBy('name')->get(), 'parents' => ParentProfile::orderBy('name')->get(), 'teachers' => Teacher::where('status', 'active')->orderBy('name')->get(), 'subjects' => Subject::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(SaveAcademicAssignmentRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            match ($data['type']) {
                'student_classroom' => Student::findOrFail($data['student_id'])->classrooms()->syncWithoutDetaching([$data['classroom_id'] => ['academic_year_id' => $data['academic_year_id'], 'joined_on' => now(), 'status' => $data['status']]]),'parent_student' => ParentProfile::findOrFail($data['parent_profile_id'])->students()->syncWithoutDetaching([$data['student_id'] => ['is_primary' => $data['is_primary'] ?? false]]),'teacher_subject' => Teacher::findOrFail($data['teacher_id'])->subjects()->syncWithoutDetaching([$data['subject_id'] => ['academic_year_id' => $data['academic_year_id']]]),'homeroom' => Classroom::findOrFail($data['classroom_id'])->update(['homeroom_teacher_id' => $data['teacher_id']])
            };
        });

        return back()->with('success', 'Penugasan berhasil disimpan.');
    }
}
