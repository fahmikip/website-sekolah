<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\SchoolProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_super_admin_can_update_school_profile(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $this->actingAs($admin)->put('/admin/school-profile', [
            'name' => 'Sekolah Indonesia Hebat', 'status' => 'Negeri', 'email' => 'info@sekolah.test',
        ])->assertRedirect()->assertSessionHas('success');
        $this->assertSame('Sekolah Indonesia Hebat', SchoolProfile::first()->name);
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user)->get('/admin/school-profile')->assertForbidden();
    }
}
