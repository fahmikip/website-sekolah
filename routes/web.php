<?php

use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');

Route::get('/dashboard', fn () => view('admin.dashboard'))->middleware(['auth', 'verified', 'permission:view_dashboard'])->name('dashboard');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/school-profile', [SchoolProfileController::class, 'edit'])->middleware('permission:manage_school_profile')->name('school-profile.edit');
    Route::put('/school-profile', [SchoolProfileController::class, 'update'])->middleware('permission:manage_school_profile')->name('school-profile.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
