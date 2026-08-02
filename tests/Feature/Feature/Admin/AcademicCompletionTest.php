<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ParentProfile;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\AcademicSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademicCompletionTest extends TestCase
{
    use RefreshDatabase;

    private function seedAndAdmin(): User
    {
        $this->seed([RolesAndPermissionsSeeder::class, AcademicSeeder::class]);

        return User::where('email', 'superadmin@example.test')->firstOrFail();
    }

    public function test_staff_and_alumni_crud_with_secure_uploads(): void
    {
        Storage::fake('public');
        $admin = $this->seedAndAdmin();
        $this->actingAs($admin)->post(route('admin.academic.store', 'staff'), [
            'employee_number' => 'STF999', 'name' => 'Staf Baru', 'gender' => 'P', 'position' => 'Laboran', 'status' => 'active',
            'photo' => UploadedFile::fake()->image('photo.jpg'), 'document' => UploadedFile::fake()->create('sk.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('admin.academic.index', 'staff'));
        $staff = Staff::where('employee_number', 'STF999')->firstOrFail();
        Storage::disk('public')->assertExists($staff->photo_path);
        Storage::disk('public')->assertExists($staff->document_path);
        $this->actingAs($admin)->post(route('admin.academic.store', 'alumni'), ['nis' => 'AL999', 'name' => 'Alumni Baru', 'graduation_year' => 2024, 'publication_consent' => 1])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('alumni', ['nis' => 'AL999', 'publication_consent' => 1]);
    }

    public function test_executable_upload_is_rejected(): void
    {
        $admin = $this->seedAndAdmin();
        $this->actingAs($admin)->post(route('admin.academic.store', 'students'), ['nis' => 'SAFE1', 'name' => 'Safe', 'gender' => 'L', 'entry_year' => 2026, 'status' => 'active', 'document' => UploadedFile::fake()->create('payload.php', 2, 'application/x-php')])->assertSessionHasErrors('document');
    }

    public function test_real_xlsx_import_and_export_work(): void
    {
        $admin = $this->seedAndAdmin();
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([['nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'address', 'previous_school', 'entry_year', 'status'], ['XLSX001', null, 'Siswa Excel', 'L', 'Jakarta', '2011-01-01', 'Islam', 'Alamat', 'SMP', 2026, 'active']]);
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        (new Xlsx($sheet))->save($path);
        $file = new UploadedFile($path, 'students.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $this->actingAs($admin)->post(route('admin.academic.students.import'), ['file' => $file])->assertSessionHas('success');
        $this->assertDatabaseHas('students', ['nis' => 'XLSX001']);
        $this->actingAs($admin)->get(route('admin.academic.export', 'students'))->assertOk()->assertDownload();
    }

    public function test_timetable_filters_period_and_is_responsive_view(): void
    {
        $admin = $this->seedAndAdmin();
        $year = AcademicYear::firstOrFail();
        $semester = Semester::firstOrFail();
        $this->actingAs($admin)->get(route('admin.academic.timetable', ['academic_year_id' => $year->id, 'semester_id' => $semester->id]))->assertOk()->assertSee('Jadwal Pelajaran')->assertSee('Senin');
    }

    public function test_action_permissions_are_enforced(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $role = Role::create(['name' => 'Academic Viewer']);
        $role->givePermissionTo('view_academic');
        $viewer = User::factory()->create(['email_verified_at' => now()]);
        $viewer->assignRole($role);
        $this->actingAs($viewer)->get(route('admin.academic.index', 'students'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.academic.create', 'students'))->assertForbidden();
        $this->actingAs($viewer)->delete(route('admin.academic.destroy', ['students', 1]))->assertForbidden();
    }

    public function test_only_one_semester_is_active_per_academic_year(): void
    {
        $admin = $this->seedAndAdmin();
        $year = AcademicYear::firstOrFail();
        $second = Semester::where('number', 2)->firstOrFail();
        $this->actingAs($admin)->put(route('admin.academic.update', ['semesters', $second->id]), ['academic_year_id' => $year->id, 'name' => $second->name, 'number' => 2, 'starts_on' => $second->starts_on->format('Y-m-d'), 'ends_on' => $second->ends_on->format('Y-m-d'), 'is_active' => 1])->assertSessionHasNoErrors();
        $this->assertSame(1, Semester::where('academic_year_id', $year->id)->where('is_active', true)->count());
        $this->assertTrue($second->fresh()->is_active);
    }

    public function test_parent_can_have_multiple_children_and_sensitive_student_data_stays_private(): void
    {
        $this->seedAndAdmin();
        $this->assertGreaterThanOrEqual(2, ParentProfile::firstOrFail()->students()->count());
        Student::firstOrFail()->update(['family_card_number' => 'SECRET-FAMILY-CARD', 'national_id_number' => 'SECRET-NIK']);
        $this->get(route('home'))->assertOk()->assertDontSee('SECRET-FAMILY-CARD')->assertDontSee('SECRET-NIK');
    }
}
