<?php

use App\Http\Controllers\Admin\NewsCategoryController as AdminNewsCategoryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/dashboard', fn () => view('admin.dashboard'))->middleware(['auth', 'verified', 'permission:view_dashboard'])->name('dashboard');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/school-profile', [SchoolProfileController::class, 'edit'])->middleware('permission:manage_school_profile')->name('school-profile.edit');
    Route::put('/school-profile', [SchoolProfileController::class, 'update'])->middleware('permission:manage_school_profile')->name('school-profile.update');
    Route::get('/news', [AdminNewsController::class, 'index'])->middleware('permission:view_news')->name('news.index');
    Route::get('/news/create', [AdminNewsController::class, 'create'])->middleware('permission:create_news')->name('news.create');
    Route::post('/news', [AdminNewsController::class, 'store'])->middleware('permission:create_news')->name('news.store');
    Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->middleware('permission:edit_news')->name('news.edit');
    Route::put('/news/{news}', [AdminNewsController::class, 'update'])->middleware('permission:edit_news')->name('news.update');
    Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->middleware('permission:delete_news')->name('news.destroy');
    Route::resource('news-categories', AdminNewsCategoryController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('permission:manage_news_categories');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
