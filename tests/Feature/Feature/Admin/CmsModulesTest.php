<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\Announcement;
use App\Models\Gallery;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_manage_cms_modules(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        foreach (['announcements', 'events', 'galleries', 'facilities', 'achievements', 'extracurriculars', 'downloads', 'faqs', 'pages', 'banners'] as $module) {
            $this->actingAs($admin)->get(route('admin.content.index', $module))->assertOk();
        }
        $this->actingAs($admin)->post(route('admin.content.store', 'announcements'), ['title' => 'Pengumuman Ujian', 'category' => 'Ujian', 'content' => 'Informasi ujian.', 'status' => 'published'])->assertRedirect(route('admin.content.index', 'announcements'));
        $this->assertDatabaseHas('announcements', ['slug' => 'pengumuman-ujian']);
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user)->get(route('admin.content.index', 'events'))->assertForbidden();
    }

    public function test_gallery_supports_multiple_secure_image_uploads(): void
    {
        Storage::fake('public');
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $gallery = Gallery::factory()->create();
        $this->actingAs($admin)->post(route('admin.gallery-items.store', $gallery), ['images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.webp')], 'caption' => 'Kegiatan'])->assertSessionHas('success');
        $this->assertCount(2, $gallery->items()->get());
    }

    public function test_announcement_is_soft_deleted(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $item = Announcement::factory()->create();
        $this->actingAs($admin)->delete(route('admin.content.destroy', ['announcements', $item->id]))->assertSessionHas('success');
        $this->assertSoftDeleted($item);
    }

    public function test_announcement_lifecycle_command_updates_status(): void
    {
        $due = Announcement::factory()->create(['status' => 'scheduled', 'published_at' => now()->subMinute()]);
        $expired = Announcement::factory()->create(['status' => 'published', 'expires_at' => now()->subMinute()]);
        $this->artisan('cms:update-publication-status')->assertSuccessful();
        $this->assertSame('published', $due->refresh()->status);
        $this->assertSame('expired', $expired->refresh()->status);
    }
}
