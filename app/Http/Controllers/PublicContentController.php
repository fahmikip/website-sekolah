<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Download;
use App\Models\Event;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Storage;

class PublicContentController extends Controller
{
    private const MODULES = [
        'pengumuman' => [Announcement::class, 'Pengumuman', 'title', 'content'], 'agenda' => [Event::class, 'Agenda', 'title', 'description'],
        'galeri' => [Gallery::class, 'Galeri', 'title', 'description'], 'fasilitas' => [Facility::class, 'Fasilitas', 'name', 'description'],
        'prestasi' => [Achievement::class, 'Prestasi', 'title', 'description'], 'ekstrakurikuler' => [Extracurricular::class, 'Ekstrakurikuler', 'name', 'description'],
        'download' => [Download::class, 'Download Center', 'title', 'description'], 'faq' => [Faq::class, 'Pertanyaan Umum', 'question', 'answer'], 'halaman' => [Page::class, 'Halaman', 'title', 'content'],
    ];

    public function index(string $module)
    {
        [$model,$label,$title,$description] = $this->config($module);
        $query = $model::query();
        match ($module) {
            'pengumuman' => $query->where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now())), 'agenda' => $query->where('status', 'published')->orderBy('starts_at'), 'halaman' => $query->where('status', 'published'), 'faq' => $query->where('is_active', true)->orderBy('sort_order'), default => $query->where('is_published', true)
        };
        $items = $query->when(request('search'), fn ($q, $s) => $q->where($title, 'like', "%{$s}%"))->paginate(12)->withQueryString();

        return view('public.content.index', [...$this->layout(), 'module' => $module, 'label' => $label, 'titleField' => $title, 'descriptionField' => $description, 'items' => $items]);
    }

    public function show(string $module, string $slug)
    {
        [$model,$label,$title,$description] = $this->config($module);
        abort_if(in_array($module, ['faq', 'download']), 404);
        $query = $model::where('slug', $slug);
        match ($module) {
            'pengumuman' => $query->where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now())),
            'agenda' => $query->where('status', 'published'),
            'halaman' => $query->where('status', 'published'),
            default => $query->where('is_published', true),
        };
        $item = $query->firstOrFail();

        return view('public.content.show', [...$this->layout(), 'module' => $module, 'label' => $label, 'titleField' => $title, 'descriptionField' => $description, 'item' => $item]);
    }

    public function download(Download $download)
    {
        abort_unless($download->is_published && Storage::disk('public')->exists($download->file_path), 404);
        $download->increment('download_count');

        return Storage::disk('public')->download($download->file_path, $download->original_name);
    }

    private function config(string $module): array
    {
        abort_unless(isset(self::MODULES[$module]), 404);

        return self::MODULES[$module];
    }

    private function layout(): array
    {
        return ['school' => SchoolProfile::first(), 'menus' => Menu::whereNull('parent_id')->where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get()];
    }
}
