<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_assets_are_valid_and_available(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/?source=pwa', $manifest['start_url']);
        $this->assertFileExists(public_path('service-worker.js'));
        $this->assertFileExists(public_path('offline.html'));
        $this->assertFileExists(public_path('icons/smart-school.svg'));
        $this->assertStringContainsString("caches.match('/offline.html')", file_get_contents(public_path('service-worker.js')));
    }

    public function test_public_website_uses_pwa_shell_and_android_button_navigation(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->get(route('home'))->assertOk()->assertSee('/manifest.webmanifest', false)->assertSee('Navigasi utama mobile')->assertSee('Beranda')->assertSee('Berita')->assertSee('Agenda')->assertSee('Kontak')->assertSee('Portal');
        $this->get(route('news.index'))->assertOk()->assertSee('Navigasi utama mobile');
        $this->get(route('content.index', 'agenda'))->assertOk()->assertSee('Navigasi utama mobile');
    }

    public function test_all_role_portals_use_modern_pwa_shell_and_button_navigation(): void
    {
        $this->seed(DatabaseSeeder::class);
        foreach ([
            'guru@example.test' => 'portal.teacher',
            'siswa@example.test' => 'portal.student',
            'orangtua@example.test' => 'portal.parent.child',
            'kepalasekolah@example.test' => 'portal.principal',
        ] as $email => $route) {
            $response = $this->actingAs(User::where('email', $email)->firstOrFail())->get(route($route));
            $response->assertOk()->assertSee('/manifest.webmanifest', false)->assertSee('Instal')->assertSee('Jadwal')->assertSee('Akademik')->assertSee('Info');
        }
    }

    public function test_backend_uses_pwa_shell_and_android_admin_navigation(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('/manifest.webmanifest', false)->assertSee('Management Console')->assertSee('Master Akademik')->assertSee('Input nilai')->assertSee('Menu');
        $this->actingAs($admin)->get(route('admin.academic.index', 'students'))->assertOk()->assertSee('/manifest.webmanifest', false)->assertSee('Management Console')->assertSee('Menu');
    }
}
