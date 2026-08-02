<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([['loc' => route('home'), 'lastmod' => now()], ['loc' => route('news.index'), 'lastmod' => now()]])
            ->concat(News::publiclyVisible()->get()->map(fn ($item) => ['loc' => route('news.show', $item->slug), 'lastmod' => $item->updated_at]))
            ->concat(Page::where('status', 'published')->get()->map(fn ($item) => ['loc' => route('content.show', ['halaman', $item->slug]), 'lastmod' => $item->updated_at]));

        return response()->view('public.sitemap', compact('urls'))->header('Content-Type', 'application/xml');
    }
}
