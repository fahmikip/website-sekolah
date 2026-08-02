<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\News;
use App\Models\SchoolProfile;

class PublicSiteController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'school' => SchoolProfile::first(),
            'menus' => Menu::with('children')->whereNull('parent_id')->where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get(),
            'latestNews' => News::with('category')->publiclyVisible()->latest('published_at')->limit(3)->get(),
            'upcomingEvents' => Event::where('status', 'published')->where('starts_at', '>=', now())->orderBy('starts_at')->limit(3)->get(),
            'achievements' => Achievement::where('is_published', true)->latest('achievement_date')->limit(3)->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->limit(5)->get(),
            'banner' => Banner::where('is_active', true)->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))->orderBy('sort_order')->first(),
            'stats' => ['Berita' => News::publiclyVisible()->count(), 'Agenda' => Event::where('status', 'published')->count(), 'Prestasi' => Achievement::where('is_published', true)->count(), 'Fasilitas' => Facility::where('is_published', true)->count(), 'Ekstrakurikuler' => Extracurricular::where('is_published', true)->count()],
        ]);
    }
}
