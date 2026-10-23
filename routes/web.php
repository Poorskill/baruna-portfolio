<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\ProfileContentController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SkillGroupController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\CvController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProjectController as PublicProjectController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', HomeController::class)->name('home');
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');
Route::get('/about', AboutController::class)->name('about');
Route::get('/projects', [PublicProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [PublicProjectController::class, 'show'])->name('projects.show');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:3,60');
Route::get('/cv', CvController::class)->name('cv.download');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('content/home', [HomeContentController::class, 'edit'])->name('content.home.edit');
    Route::put('content/home', [HomeContentController::class, 'update'])->name('content.home.update');

    Route::get('content/profile', [ProfileContentController::class, 'edit'])->name('content.profile.edit');
    Route::put('content/profile', [ProfileContentController::class, 'update'])->name('content.profile.update');

    Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

    Route::resource('projects', AdminProjectController::class);
    Route::delete('projects/{project}/images/{image}', [AdminProjectController::class, 'destroyImage'])->name('projects.images.destroy');

    Route::get('skill-groups', [SkillGroupController::class, 'index'])->name('skill-groups.index');
    Route::post('skill-groups', [SkillGroupController::class, 'store'])->name('skill-groups.store');
    Route::put('skill-groups/{skillGroup}', [SkillGroupController::class, 'update'])->name('skill-groups.update');
    Route::delete('skill-groups/{skillGroup}', [SkillGroupController::class, 'destroy'])->name('skill-groups.destroy');

    Route::get('skills', [SkillController::class, 'index'])->name('skills.index');
    Route::post('skills', [SkillController::class, 'store'])->name('skills.store');
    Route::put('skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');

    Route::get('experience', [ExperienceController::class, 'index'])->name('experience.index');
    Route::post('experience', [ExperienceController::class, 'store'])->name('experience.store');
    Route::put('experience/{experience}', [ExperienceController::class, 'update'])->name('experience.update');
    Route::delete('experience/{experience}', [ExperienceController::class, 'destroy'])->name('experience.destroy');

    Route::get('education', [EducationController::class, 'index'])->name('education.index');
    Route::post('education', [EducationController::class, 'store'])->name('education.store');
    Route::put('education/{education}', [EducationController::class, 'update'])->name('education.update');
    Route::delete('education/{education}', [EducationController::class, 'destroy'])->name('education.destroy');

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::put('certificates/{certificate}', [CertificateController::class, 'update'])->name('certificates.update');
    Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');

    Route::get('navigation', [NavigationController::class, 'index'])->name('navigation.index');
    Route::post('navigation', [NavigationController::class, 'store'])->name('navigation.store');
    Route::put('navigation/{navigation}', [NavigationController::class, 'update'])->name('navigation.update');
    Route::delete('navigation/{navigation}', [NavigationController::class, 'destroy'])->name('navigation.destroy');

    Route::get('social-links', [SocialLinkController::class, 'index'])->name('social-links.index');
    Route::post('social-links', [SocialLinkController::class, 'store'])->name('social-links.store');
    Route::put('social-links/{socialLink}', [SocialLinkController::class, 'update'])->name('social-links.update');
    Route::delete('social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');

    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
    Route::put('messages/{message}/read', [ContactMessageController::class, 'markRead'])->name('messages.read');

    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');

    Route::get('account', [ProfileController::class, 'edit'])->name('account.edit');
    Route::patch('account', [ProfileController::class, 'update'])->name('account.update');
    Route::delete('account', [ProfileController::class, 'destroy'])->name('account.destroy');
});

require __DIR__.'/auth.php';
