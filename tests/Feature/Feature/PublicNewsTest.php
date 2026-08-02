<?php

namespace Tests\Feature\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_only_shows_published_news(): void
    {
        $category = NewsCategory::factory()->create();
        $published = News::factory()->create(['news_category_id' => $category->id, 'title' => 'Berita Publik', 'slug' => 'berita-publik']);
        News::factory()->create(['news_category_id' => $category->id, 'title' => 'Berita Draft', 'slug' => 'berita-draft', 'status' => 'draft', 'published_at' => null]);
        News::factory()->create(['news_category_id' => $category->id, 'title' => 'Berita Masa Depan', 'slug' => 'berita-masa-depan', 'status' => 'published', 'published_at' => now()->addDay()]);

        $this->get(route('news.index'))->assertOk()->assertSee($published->title)->assertDontSee('Berita Draft')->assertDontSee('Berita Masa Depan');
    }

    public function test_public_can_read_news_and_view_count_increases(): void
    {
        $news = News::factory()->create(['view_count' => 0]);
        $this->get(route('news.show', $news->slug))->assertOk()->assertSee($news->title)->assertSee('og:type');
        $this->assertSame(1, $news->refresh()->view_count);
    }

    public function test_draft_news_returns_not_found(): void
    {
        $news = News::factory()->create(['status' => 'draft', 'published_at' => null]);
        $this->get(route('news.show', $news->slug))->assertNotFound();
    }
}
