<?php

namespace Tests\Feature\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_and_update_news(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $category = NewsCategory::factory()->create();
        $payload = ['news_category_id' => $category->id, 'title' => 'Kegiatan Literasi Digital', 'content' => 'Isi berita sekolah.', 'status' => 'published', 'is_featured' => 1, 'tags' => 'akademik, literasi'];

        $this->actingAs($admin)->post(route('admin.news.store'), $payload)->assertRedirect(route('admin.news.index'));
        $news = News::firstOrFail();
        $this->assertSame('kegiatan-literasi-digital', $news->slug);
        $this->assertNotNull($news->published_at);
        $this->assertCount(2, $news->tags);

        $this->actingAs($admin)->put(route('admin.news.update', $news), [...$payload, 'title' => 'Kegiatan Literasi Digital Terbaru'])->assertRedirect(route('admin.news.index'));
        $this->assertSame('Kegiatan Literasi Digital Terbaru', $news->refresh()->title);
    }

    public function test_user_without_permission_cannot_manage_news(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
    }

    public function test_scheduled_news_requires_future_date(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $category = NewsCategory::factory()->create();
        $this->actingAs($admin)->post(route('admin.news.store'), ['news_category_id' => $category->id, 'title' => 'Berita Terjadwal', 'content' => 'Isi.', 'status' => 'scheduled', 'published_at' => now()->subHour()->format('Y-m-d H:i:s')])->assertSessionHasErrors('published_at');
        $this->assertDatabaseCount('news', 0);
    }

    public function test_deleted_news_uses_soft_delete(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::where('email', 'superadmin@example.test')->firstOrFail();
        $news = News::factory()->create(['author_id' => $admin->id]);
        $this->actingAs($admin)->delete(route('admin.news.destroy', $news))->assertSessionHas('success');
        $this->assertSoftDeleted($news);
    }

    public function test_scheduler_publishes_due_news(): void
    {
        $news = News::factory()->create(['status' => 'scheduled', 'published_at' => now()->subMinute()]);
        $this->artisan('news:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $news->refresh()->status);
    }
}
