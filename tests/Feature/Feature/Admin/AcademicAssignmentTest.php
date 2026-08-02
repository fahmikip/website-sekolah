<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\AcademicSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AcademicAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed([RolesAndPermissionsSeeder::class, AcademicSeeder::class]);

        return User::where('email', 'superadmin@example.test')->first();
    }

    public function test_assignments_are_saved(): void
    {
        $admin = $this->admin();
        $student = Student::first();
        $class = Classroom::find(2);
        $year = AcademicYear::first();
        $this->actingAs($admin)->post(route('admin.academic.assignments.store'), ['type' => 'student_classroom', 'student_id' => $student->id, 'classroom_id' => $class->id, 'academic_year_id' => $year->id, 'status' => 'active'])->assertSessionHas('success');
        $parent = ParentProfile::first();
        $this->actingAs($admin)->post(route('admin.academic.assignments.store'), ['type' => 'parent_student', 'parent_profile_id' => $parent->id, 'student_id' => $student->id, 'is_primary' => 1])->assertSessionHas('success');
        $teacher = Teacher::first();
        $subject = Subject::first();
        $this->actingAs($admin)->post(route('admin.academic.assignments.store'), ['type' => 'teacher_subject', 'teacher_id' => $teacher->id, 'subject_id' => $subject->id, 'academic_year_id' => $year->id])->assertSessionHas('success');
        $this->actingAs($admin)->post(route('admin.academic.assignments.store'), ['type' => 'homeroom', 'teacher_id' => $teacher->id, 'classroom_id' => $class->id])->assertSessionHas('success');
        $this->assertSame($teacher->id, $class->refresh()->homeroom_teacher_id);
    }

    public function test_students_can_be_imported_and_exported_as_csv(): void
    {
        $admin = $this->admin();
        $csv = "nis,nisn,name,gender,birth_place,birth_date,religion,address,previous_school,entry_year,status\nNEW001,,Siswa Baru,L,Jakarta,2011-01-01,Islam,Alamat,SMP,2026,active\n";
        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);
        $this->actingAs($admin)->post(route('admin.academic.students.import'), ['file' => $file])->assertSessionHas('success');
        $this->assertDatabaseHas('students', ['nis' => 'NEW001']);
        $this->actingAs($admin)->get(route('admin.academic.export', 'students'))->assertOk()->assertDownload();
    }
}
