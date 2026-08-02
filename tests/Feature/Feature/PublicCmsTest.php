<?php

namespace Tests\Feature\Feature;

use Database\Seeders\CmsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_cms_listings_are_available(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, CmsSeeder::class]);
        foreach (['pengumuman', 'agenda', 'galeri', 'fasilitas', 'prestasi', 'ekstrakurikuler', 'download', 'faq', 'halaman'] as $module) {
            $this->get(route('content.index', $module))->assertOk();
        }
    }

    public function test_homepage_contains_dynamic_cms_data(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, CmsSeeder::class]);
        $this->get(route('home'))->assertOk()->assertSee('Pameran Karya Siswa')->assertSee('Juara Olimpiade Sains')->assertSee('Apa jam layanan sekolah?');
    }

    public function test_sitemap_is_xml_and_contains_published_pages(): void
    {
        $this->seed([RolesAndPermissionsSeeder::class, CmsSeeder::class]);
        $this->get(route('sitemap'))->assertOk()->assertHeader('Content-Type', 'application/xml')->assertSee('visi-misi');
    }
}
