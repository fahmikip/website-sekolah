<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryItemsRequest;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Support\Facades\Storage;

class GalleryItemController extends Controller
{
    public function store(StoreGalleryItemsRequest $request, Gallery $gallery)
    {
        foreach ($request->file('images', []) as $index => $image) {
            $gallery->items()->create(['type' => 'image', 'file_path' => $image->store('cms/galleries/items', 'public'), 'caption' => $request->caption, 'sort_order' => $gallery->items()->count() + $index]);
        }
        if ($request->filled('video_url')) {
            $gallery->items()->create(['type' => 'video', 'video_url' => $request->video_url, 'caption' => $request->caption, 'sort_order' => $gallery->items()->count()]);
        }

        return back()->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function destroy(GalleryItem $galleryItem)
    {
        if ($galleryItem->file_path) {
            Storage::disk('public')->delete($galleryItem->file_path);
        } $galleryItem->delete();

        return back()->with('success', 'Item galeri dihapus.');
    }
}
