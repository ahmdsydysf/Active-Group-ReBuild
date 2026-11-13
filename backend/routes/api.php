<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\SiteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', HealthController::class)->name('health');

    // Public site (read-only)
    Route::middleware('throttle:public')->group(function () {
        Route::get('/site', [SiteController::class, 'site'])->name('site');
        Route::get('/home', [SiteController::class, 'home'])->name('home');
    });
    Route::post('/newsletter', NewsletterController::class)->middleware('throttle:forms')->name('newsletter');

    // Dashboard
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:login')->name('login');

        Route::middleware(['auth:sanctum', 'active', 'throttle:admin'])->group(function () {
            Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [Admin\AuthController::class, 'me'])->name('me');
            Route::get('/overview', Admin\OverviewController::class)->name('overview');
            Route::get('/settings', [Admin\SettingsController::class, 'show'])->name('settings.show');
            Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

            foreach ([
                'slides' => Admin\SlideController::class,
                'testimonials' => Admin\TestimonialController::class,
                'partners' => Admin\PartnerController::class,
            ] as $uri => $controller) {
                Route::post("/{$uri}/reorder", [$controller, 'reorder'])->name("{$uri}.reorder");
                Route::apiResource($uri, $controller)->except('show')->parameters([$uri => 'id'])->whereNumber('id');
            }
        });
    });
});
