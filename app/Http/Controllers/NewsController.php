<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\SchoolProfile;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['category', 'author'])->publiclyVisible()
            ->when(request('search'), fn ($q, $search) => $q->where(fn ($query) => $query->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%")))
            ->when(request('category'), fn ($q, $slug) => $q->whereHas('category', fn ($category) => $category->where('slug', $slug)))
            ->latest('published_at')->paginate(9)->withQueryString();

        return view('public.news.index', [...$this->layoutData(), 'news' => $news, 'categories' => NewsCategory::where('is_active', true)->orderBy('name')->get()]);
    }

    public function show(string $slug)
    {
        $news = News::with(['category', 'author'])->publiclyVisible()->where('slug', $slug)->firstOrFail();
        $news->increment('view_count');
        $related = News::with('category')->publiclyVisible()->where('news_category_id', $news->news_category_id)->whereKeyNot($news->id)->latest('published_at')->limit(3)->get();

        return view('public.news.show', [...$this->layoutData(), 'news' => $news, 'related' => $related]);
    }

    private function layoutData(): array
    {
        return ['school' => SchoolProfile::first(), 'menus' => Menu::with('children')->whereNull('parent_id')->where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get()];
    }
}
