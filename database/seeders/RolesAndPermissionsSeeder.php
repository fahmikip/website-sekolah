<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\SchoolProfile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['view_dashboard', 'manage_school_profile', 'manage_settings', 'manage_navigation', 'manage_users', 'manage_roles', 'view_news', 'create_news', 'edit_news', 'delete_news', 'manage_news_categories', 'manage_cms', 'manage_academic'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        foreach (['Super Admin', 'Administrator', 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Operator', 'Guru', 'Wali Kelas', 'Guru BK', 'Siswa', 'Orang Tua', 'Petugas Perpustakaan', 'Panitia PPDB'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        Role::findByName('Super Admin')->syncPermissions(Permission::all());
        Role::findByName('Administrator')->syncPermissions($permissions);
        Role::findByName('Kepala Sekolah')->syncPermissions(['view_dashboard']);
        $admin = User::updateOrCreate(['email' => 'superadmin@example.test'], ['name' => 'Super Administrator', 'password' => Hash::make('password'), 'email_verified_at' => now()]);
        $admin->syncRoles(['Super Admin']);
        SchoolProfile::firstOrCreate([], [
            'name' => 'SMARTECH Nusantara', 'npsn' => '12345678', 'level' => 'SMA', 'status' => 'Negeri', 'accreditation' => 'A',
            'founded_year' => 1998, 'tagline' => 'Tumbuh, Berdaya, Menginspirasi',
            'short_description' => 'Ekosistem pendidikan modern yang memadukan karakter, literasi, dan teknologi untuk masa depan Indonesia.',
            'address' => 'Jl. Pendidikan No. 1', 'city' => 'Jakarta', 'province' => 'DKI Jakarta', 'email' => 'halo@smartech.sch.id', 'phone' => '(021) 555-0101',
        ]);
        foreach ([
            ['group' => 'theme', 'key' => 'theme.primary_color', 'value' => '#14532d', 'type' => 'color', 'is_public' => true],
            ['group' => 'theme', 'key' => 'theme.secondary_color', 'value' => '#f59e0b', 'type' => 'color', 'is_public' => true],
            ['group' => 'website', 'key' => 'website.hero_eyebrow', 'value' => 'Sekolah Masa Depan', 'type' => 'text', 'is_public' => true],
        ] as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
        foreach ([['Beranda', '/', 10], ['Profil', '/#profil', 20], ['Program', '/#program', 30], ['Berita', '/berita', 40], ['Kontak', '/#kontak', 50]] as [$label,$url,$order]) {
            Menu::firstOrCreate(['label' => $label, 'location' => 'header'], ['url' => $url, 'sort_order' => $order, 'is_active' => true]);
        }
    }
}
