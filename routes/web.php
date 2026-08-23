<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BenchmarkController;
use App\Http\Controllers\BenchmarkExportController;
use App\Http\Controllers\BenchmarkImportController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\PromptExportController;
use App\Http\Controllers\PromptImportController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Public marketing site
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/features', [LandingController::class, 'features'])->name('features');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LandingController::class, 'terms'])->name('terms');

/*
|--------------------------------------------------------------------------
| Guest authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:auth')->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:auth')->name('login.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:auth')->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:auth')->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated application
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/welcome', [OnboardingController::class, 'welcome'])->name('onboarding.welcome');
    Route::post('/welcome/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');

    Route::resource('prompts', PromptController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
    Route::post('/prompts/{prompt}/playground', [PromptController::class, 'playground'])->name('prompts.playground');
    Route::post('/prompts/{prompt}/versions/{version}/restore', [PromptController::class, 'restoreVersion'])
        ->name('prompts.restore');
    Route::get('/prompts/{prompt}/export', PromptExportController::class)->name('prompts.export');
    Route::post('/prompts/import', [PromptImportController::class, '__invoke'])->name('prompts.import');
    Route::resource('collections', CollectionController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/benchmarks/wizard', [BenchmarkController::class, 'create'])->name('benchmarks.create');
    Route::get('/benchmarks/{benchmark}/export', BenchmarkExportController::class)
        ->name('benchmarks.export');
    Route::resource('benchmarks', BenchmarkController::class)->except(['create']);
    Route::post('/benchmarks/import', [BenchmarkImportController::class, '__invoke'])
        ->name('benchmarks.import');

    Route::resource('runs', RunController::class)->only(['index', 'create', 'show']);
    Route::post('/runs', [RunController::class, 'store'])
        ->middleware('throttle:runs')
        ->name('runs.store');
    Route::get('/runs/{run}/status', [RunController::class, 'status'])->name('runs.status');
    Route::post('/runs/{run}/cancel', [RunController::class, 'cancel'])->name('runs.cancel');

    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::post('/audit', [AuditLogController::class, 'store'])->name('audit.store');
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::post('/marketplace/{item}/install', [MarketplaceController::class, 'install'])->name('marketplace.install');
    Route::post('/marketplace/publish', [MarketplaceController::class, 'publish'])->name('marketplace.publish');
    Route::post('/marketplace/{item}/report', [MarketplaceController::class, 'report'])
        ->middleware('throttle:10,1')
        ->name('marketplace.report');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/unread', [NotificationController::class, 'markUnread'])
        ->name('notifications.unread');
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::get('/settings/billing', [SettingsController::class, 'billing'])->name('settings.billing');
    Route::get('/settings', fn () => redirect()->route('settings.profile'));
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])
        ->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::get('/search', SearchController::class)->name('search');
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::post('/teams/{team}/invite', [TeamController::class, 'invite'])->name('teams.invite');
    Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/logout-others', [SettingsController::class, 'logoutOthers'])->name('settings.logout-others');
});

/*
|--------------------------------------------------------------------------
| Administration (is_admin only)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    Route::get('/', App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/marketplace', [App\Http\Controllers\Admin\MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::post('/marketplace/{item}/listed', [App\Http\Controllers\Admin\MarketplaceController::class, 'setListed'])->name('marketplace.listed');
    Route::get('/reports', [App\Http\Controllers\Admin\MarketplaceController::class, 'reports'])->name('reports.index');
    Route::post('/reports/{report}/resolve/{action}', [App\Http\Controllers\Admin\MarketplaceController::class, 'resolve'])
        ->whereIn('action', ['dismiss', 'unlist'])
        ->name('reports.resolve');
});
