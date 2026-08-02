<?php

use App\Http\Controllers\Admin\AcademicAssignmentController;
use App\Http\Controllers\Admin\AcademicExportController;
use App\Http\Controllers\Admin\AcademicMasterController;
use App\Http\Controllers\Admin\AssessmentConfigurationController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\ContentModuleController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\InterventionController;
use App\Http\Controllers\Admin\NewsCategoryController as AdminNewsCategoryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\ScoreEntryController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\ReportVerificationController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/konten/{module}', [PublicContentController::class, 'index'])->name('content.index');
Route::get('/konten/{module}/{slug}', [PublicContentController::class, 'show'])->name('content.show');
Route::get('/unduhan/{download}', [PublicContentController::class, 'download'])->name('downloads.file')->middleware('throttle:30,1');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/verifikasi-rapor/{token}', ReportVerificationController::class)->name('reports.verify')->middleware('throttle:30,1');

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
    Route::middleware('permission:manage_cms')->group(function () {
        Route::post('/galleries/{gallery}/items', [GalleryItemController::class, 'store'])->name('gallery-items.store');
        Route::delete('/gallery-items/{galleryItem}', [GalleryItemController::class, 'destroy'])->name('gallery-items.destroy');
        Route::get('/content/{module}', [ContentModuleController::class, 'index'])->name('content.index');
        Route::get('/content/{module}/create', [ContentModuleController::class, 'create'])->name('content.create');
        Route::post('/content/{module}', [ContentModuleController::class, 'store'])->name('content.store');
        Route::get('/content/{module}/{id}/edit', [ContentModuleController::class, 'edit'])->name('content.edit');
        Route::put('/content/{module}/{id}', [ContentModuleController::class, 'update'])->name('content.update');
        Route::delete('/content/{module}/{id}', [ContentModuleController::class, 'destroy'])->name('content.destroy');
    });
    Route::middleware('permission:view_academic|manage_academic')->group(function () {
        Route::get('/timetable', TimetableController::class)->name('academic.timetable');
        Route::get('/academic-assignments', [AcademicAssignmentController::class, 'index'])->name('academic.assignments');
        Route::post('/academic-assignments', [AcademicAssignmentController::class, 'store'])->name('academic.assignments.store');
        Route::get('/academic-export/{type}', [AcademicExportController::class, 'export'])->name('academic.export');
        Route::post('/academic-import/students', [AcademicExportController::class, 'importStudents'])->name('academic.students.import');
        Route::get('/academic/{module}', [AcademicMasterController::class, 'index'])->name('academic.index');
        Route::get('/academic/{module}/create', [AcademicMasterController::class, 'create'])->name('academic.create');
        Route::post('/academic/{module}', [AcademicMasterController::class, 'store'])->name('academic.store');
        Route::get('/academic/{module}/{id}/edit', [AcademicMasterController::class, 'edit'])->name('academic.edit');
        Route::put('/academic/{module}/{id}', [AcademicMasterController::class, 'update'])->name('academic.update');
        Route::delete('/academic/{module}/{id}', [AcademicMasterController::class, 'destroy'])->name('academic.destroy');
    });
    Route::middleware('permission:manage_assessment')->group(function () {
        Route::resource('assessments', AssessmentController::class)->except('show');
        Route::get('/assessment/interventions/{module}', [InterventionController::class, 'index'])->name('assessment.interventions.index');
        Route::get('/assessment/interventions/{module}/create', [InterventionController::class, 'create'])->name('assessment.interventions.create');
        Route::post('/assessment/interventions/{module}', [InterventionController::class, 'store'])->name('assessment.interventions.store');
        Route::get('/assessment/interventions/{module}/{id}/edit', [InterventionController::class, 'edit'])->name('assessment.interventions.edit');
        Route::put('/assessment/interventions/{module}/{id}', [InterventionController::class, 'update'])->name('assessment.interventions.update');
        Route::delete('/assessment/interventions/{module}/{id}', [InterventionController::class, 'destroy'])->name('assessment.interventions.destroy');
        Route::get('/assessment/config/{module}', [AssessmentConfigurationController::class, 'index'])->name('assessment.config.index');
        Route::get('/assessment/config/{module}/create', [AssessmentConfigurationController::class, 'create'])->name('assessment.config.create');
        Route::post('/assessment/config/{module}', [AssessmentConfigurationController::class, 'store'])->name('assessment.config.store');
        Route::get('/assessment/config/{module}/{id}/edit', [AssessmentConfigurationController::class, 'edit'])->name('assessment.config.edit');
        Route::put('/assessment/config/{module}/{id}', [AssessmentConfigurationController::class, 'update'])->name('assessment.config.update');
        Route::delete('/assessment/config/{module}/{id}', [AssessmentConfigurationController::class, 'destroy'])->name('assessment.config.destroy');
    });
    Route::get('/assessment/scores', [ScoreEntryController::class, 'index'])->middleware('permission:input_scores')->name('assessment.scores.index');
    Route::get('/assessment/scores/{assessment}', [ScoreEntryController::class, 'edit'])->middleware('permission:input_scores')->name('assessment.scores.edit');
    Route::put('/assessment/scores/{assessment}', [ScoreEntryController::class, 'update'])->middleware('permission:input_scores')->name('assessment.scores.update');
    Route::post('/assessment/scores/{assessment}/autosave', [ScoreEntryController::class, 'autosave'])->middleware(['permission:input_scores', 'throttle:120,1'])->name('assessment.scores.autosave');
    Route::get('/assessment/scores/{assessment}/export', [ScoreEntryController::class, 'export'])->middleware('permission:input_scores')->name('assessment.scores.export');
    Route::post('/assessment/scores/{assessment}/import', [ScoreEntryController::class, 'import'])->middleware('permission:input_scores')->name('assessment.scores.import');
    Route::get('/assessment/analysis/{assessment}/export', [ScoreEntryController::class, 'exportAnalysis'])->middleware('permission:manage_assessment')->name('assessment.analysis.export');
    Route::patch('/assessment/student-scores/{studentScore}/status', [ScoreEntryController::class, 'transition'])->name('assessment.scores.transition');
    Route::get('/assessment/analysis/{assessment}', [ScoreEntryController::class, 'analysis'])->middleware('permission:manage_assessment')->name('assessment.analysis');
    Route::middleware('permission:manage_report_cards')->group(function () {
        Route::get('/reports', [ReportCardController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportCardController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/{reportCard}', [ReportCardController::class, 'show'])->name('reports.show');
        Route::put('/reports/{reportCard}', [ReportCardController::class, 'update'])->name('reports.update');
        Route::patch('/reports/{reportCard}/status', [ReportCardController::class, 'transition'])->name('reports.transition');
        Route::get('/reports/{reportCard}/print', [ReportCardController::class, 'print'])->name('reports.print');
        Route::get('/reports/{reportCard}/pdf', [ReportCardController::class, 'pdf'])->name('reports.pdf');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/portal/guru', [PortalController::class, 'teacher'])->middleware('permission:view_teacher_portal')->name('portal.teacher');
    Route::get('/portal/siswa', [PortalController::class, 'student'])->middleware('permission:view_student_portal')->name('portal.student');
    Route::get('/portal/orang-tua/{student?}', [PortalController::class, 'parent'])->middleware('permission:view_parent_portal')->name('portal.parent.child');
    Route::get('/portal/kepala-sekolah', [PortalController::class, 'principal'])->middleware('permission:view_principal_dashboard')->name('portal.principal');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
