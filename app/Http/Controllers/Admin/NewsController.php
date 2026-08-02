<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function __construct(private readonly NewsService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::with(['category', 'author'])->when(request('search'), fn ($q, $search) => $q->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%")))->when(request('status'), fn ($q, $status) => $q->where('status', $status))->latest()->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.news.create', ['categories' => NewsCategory::where('is_active', true)->orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        $this->service->store($request->safe()->except(['featured_image', 'og_image', 'tags']), $request->user()->id, $request->file('featured_image'), $request->file('og_image'), (string) $request->string('tags'));

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return redirect()->route('admin.news.edit', $news);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', ['news' => $news, 'categories' => NewsCategory::where('is_active', true)->orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        $this->service->update($news, $request->safe()->except(['featured_image', 'og_image', 'tags']), $request->file('featured_image'), $request->file('og_image'), (string) $request->string('tags'));

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $this->service->delete($news);

        return back()->with('success', 'Berita dipindahkan ke arsip sampah.');
    }
}
