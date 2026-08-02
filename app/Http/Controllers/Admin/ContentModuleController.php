<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveContentModuleRequest;
use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Download;
use App\Models\Event;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentModuleController extends Controller
{
    private const MODULES = [
        'announcements' => [Announcement::class, 'Pengumuman', 'title', 'content'], 'events' => [Event::class, 'Agenda', 'title', 'description'],
        'galleries' => [Gallery::class, 'Galeri', 'title', 'description'], 'facilities' => [Facility::class, 'Fasilitas', 'name', 'description'],
        'achievements' => [Achievement::class, 'Prestasi', 'title', 'description'], 'extracurriculars' => [Extracurricular::class, 'Ekstrakurikuler', 'name', 'description'],
        'downloads' => [Download::class, 'Download', 'title', 'description'], 'faqs' => [Faq::class, 'FAQ', 'question', 'answer'],
        'pages' => [Page::class, 'Halaman', 'title', 'content'], 'banners' => [Banner::class, 'Banner', 'title', 'subtitle'],
    ];

    public function index(string $module, Request $request)
    {
        [$model,$label,$title,$description] = $this->config($module);
        $items = $model::query()->when($request->search, fn ($q, $s) => $q->where(fn ($x) => $x->where($title, 'like', "%{$s}%")->orWhere($description, 'like', "%{$s}%")))->latest()->paginate(12)->withQueryString();

        return view('admin.content.index', compact('module', 'label', 'items', 'title'));
    }

    public function create(string $module)
    {
        [, $label] = $this->config($module);

        return view('admin.content.form', compact('module', 'label'));
    }

    public function edit(string $module, int $id)
    {
        [$model,$label] = $this->config($module);
        $item = $model::findOrFail($id);

        return view('admin.content.form', compact('module', 'label', 'item'));
    }

    public function store(SaveContentModuleRequest $request, string $module)
    {
        [$model] = $this->config($module);
        $data = $this->prepare($request, $module);
        $model::create($data);

        return redirect()->route('admin.content.index', $module)->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(SaveContentModuleRequest $request, string $module, int $id)
    {
        [$model] = $this->config($module);
        $item = $model::findOrFail($id);
        $item->update($this->prepare($request, $module, $item));

        return redirect()->route('admin.content.index', $module)->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(string $module, int $id)
    {
        [$model] = $this->config($module);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    private function config(string $module): array
    {
        abort_unless(isset(self::MODULES[$module]), 404);

        return self::MODULES[$module];
    }

    private function prepare(SaveContentModuleRequest $request, string $module, $item = null): array
    {
        $data = $request->safe()->except(['image', 'file']);
        $slugSource = $data['title'] ?? $data['name'] ?? null;
        if ($slugSource && in_array($module, ['announcements', 'events', 'galleries', 'facilities', 'achievements', 'extracurriculars', 'downloads', 'pages'])) {
            $data['slug'] = $this->slug(self::MODULES[$module][0], $slugSource, $item?->id);
        }
        $imageColumn = match ($module) {
            'events' => 'poster_path','galleries' => 'cover_path','facilities','achievements','extracurriculars' => 'image_path','banners' => 'image_path',default => null
        };
        if ($imageColumn && $request->hasFile('image')) {
            if ($item?->{$imageColumn}) {
                Storage::disk('public')->delete($item->{$imageColumn});
            } $data[$imageColumn] = $request->file('image')->store("cms/{$module}", 'public');
        }
        if ($module === 'downloads' && $request->hasFile('file')) {
            $file = $request->file('file');
            if ($item?->file_path) {
                Storage::disk('public')->delete($item->file_path);
            } $data += ['file_path' => $file->store('downloads', 'public'), 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize()];
        }

        return $data;
    }

    private function slug(string $model, string $value, ?int $ignore = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $i = 2;
        while ($model::withTrashed()->where('slug', $slug)->when($ignore, fn ($q) => $q->whereKeyNot($ignore))->exists()) {
            $slug = $base.'-'.$i++;
        }

return $slug;
    }
}
