<?php

namespace App\Http\Controllers;

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
        ]);
    }
}
