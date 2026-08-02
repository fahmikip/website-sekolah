<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Services\ReportCardService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardAndPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_generation_and_private_verification(): void
    {
        $this->seed(DatabaseSeeder::class);
        $student = Student::firstOrFail();
        $classroom = $student->classrooms()->firstOrFail();
        $year = AcademicYear::where('is_active', true)->firstOrFail();
        $semester = Semester::where('is_active', true)->firstOrFail();
        $report = app(ReportCardService::class)->generate($student, $year->id, $semester->id, $classroom->id);
        $this->get(route('reports.verify', $report->verification_token))->assertNotFound();
        $report->update(['status' => 'published', 'published_at' => now()]);
        $this->get(route('reports.verify', $report->verification_token))->assertOk()->assertSee($student->name);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.reports.pdf', $report))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_parent_cannot_open_unrelated_child(): void
    {
        $this->seed(DatabaseSeeder::class);
        $parent = User::where('email', 'orangtua@example.test')->firstOrFail();
        $unrelated = Student::whereDoesntHave('parents', fn ($q) => $q->where('parent_profiles.id', $parent->parentProfile->id))->firstOrFail();
        $this->actingAs($parent)->get(route('portal.parent.child', $unrelated))->assertForbidden();
    }

    public function test_each_portal_requires_its_permission(): void
    {
        $this->seed(DatabaseSeeder::class);
        $student = User::where('email', 'siswa@example.test')->firstOrFail();
        $this->actingAs($student)->get(route('portal.student'))->assertOk();
        $this->actingAs($student)->get(route('portal.teacher'))->assertForbidden();
    }
}
