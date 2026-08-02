<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\AcademicSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicMasterTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_all_academic_modules(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, AcademicSeeder::class]);
        $admin = User::where('email', 'superadmin@example.test')->first();
        foreach (['academic-years', 'semesters', 'curricula', 'phases', 'levels', 'classrooms', 'subjects', 'teachers', 'staff', 'students', 'parents', 'alumni', 'schedules'] as $module) {
            $this->actingAs($admin)->get(route('admin.academic.index', $module))->assertOk();
        }
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user)->get(route('admin.academic.index', 'students'))->assertForbidden();
    }

    public function test_schedule_conflicts_are_rejected(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, AcademicSeeder::class]);
        $admin = User::where('email', 'superadmin@example.test')->first();
        $existing = Schedule::first();
        $payload = ['academic_year_id' => AcademicYear::first()->id, 'semester_id' => Semester::first()->id, 'classroom_id' => Classroom::first()->id, 'subject_id' => Subject::first()->id, 'teacher_id' => $existing->teacher_id, 'day_of_week' => $existing->day_of_week, 'starts_at' => '07:30', 'ends_at' => '09:00', 'room' => 'Ruang Lain'];
        $this->actingAs($admin)->post(route('admin.academic.store', 'schedules'), $payload)->assertSessionHasErrors('starts_at');
    }

    public function test_seed_contains_required_demo_data(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, AcademicSeeder::class]);
        $this->assertDatabaseCount('students', 100);
        $this->assertDatabaseCount('teachers', 10);
        $this->assertDatabaseCount('classrooms', 6);
        $this->assertDatabaseCount('subjects', 10);
        $this->assertDatabaseCount('staff', 5);
        $this->assertDatabaseCount('alumni', 10);
    }
}
