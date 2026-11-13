<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\ContactController;
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
        Route::get('/catalog', [SiteController::class, 'catalog'])->name('catalog');
        Route::get('/posts', [SiteController::class, 'posts'])->name('posts');
        Route::get('/about', [SiteController::class, 'about'])->name('about');
        Route::get('/pages', [SiteController::class, 'pages'])->name('pages');
    });
    Route::post('/newsletter', NewsletterController::class)->middleware('throttle:forms')->name('newsletter');
    Route::post('/contact', ContactController::class)->middleware('throttle:forms')->name('contact');

    // Dashboard
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:login')->name('login');
        Route::middleware('throttle:login')->group(function () {
            Route::post('/forgot-password', [Admin\PasswordController::class, 'forgot'])->name('password.forgot');
            Route::post('/reset-password', [Admin\PasswordController::class, 'reset'])->name('password.reset');
        });

        Route::middleware(['auth:sanctum', 'active', 'throttle:admin'])->group(function () {
            Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [Admin\AuthController::class, 'me'])->name('me');
            Route::get('/overview', Admin\OverviewController::class)->name('overview');
            Route::get('/publish', [Admin\PublishController::class, 'show'])->name('publish.show');
            Route::post('/publish', [Admin\PublishController::class, 'store'])->middleware('throttle:forms')->name('publish');

            // Content sections: list, show, create, update, delete, plus reorder and bulk actions.
            foreach ([
                'slides' => Admin\SlideController::class,
                'testimonials' => Admin\TestimonialController::class,
                'partners' => Admin\PartnerController::class,
                'product-categories' => Admin\CategoryController::class,
                'product-types' => Admin\ProductTypeController::class,
                'factories' => Admin\FactoryController::class,
                'products' => Admin\ProductController::class,
                'services' => Admin\ServiceController::class,
                'posts' => Admin\PostController::class,
                'post-categories' => Admin\PostCategoryController::class,
                'team' => Admin\TeamMemberController::class,
                'features' => Admin\FeatureController::class,
                'gallery-albums' => Admin\GalleryAlbumController::class,
                'gallery-photos' => Admin\GalleryPhotoController::class,
            ] as $uri => $controller) {
                if ($uri !== 'posts') { // articles are ordered by date
                    Route::post("/{$uri}/reorder", [$controller, 'reorder'])->name("{$uri}.reorder");
                }
                Route::post("/{$uri}/bulk", [$controller, 'bulk'])->name("{$uri}.bulk");
                Route::apiResource($uri, $controller)->parameters([$uri => 'id'])->whereNumber('id');
            }
            Route::post('/gallery-photos/upload', [Admin\GalleryPhotoController::class, 'upload'])->name('gallery-photos.upload');

            Route::get('/messages/export', [Admin\MessageController::class, 'export'])->name('messages.export');
            Route::post('/messages/{id}/reply', [Admin\MessageController::class, 'reply'])->whereNumber('id')->middleware('throttle:forms')->name('messages.reply');
            Route::apiResource('messages', Admin\MessageController::class)->except('store')->parameters(['messages' => 'id'])->whereNumber('id');

            Route::get('/subscribers/export', [Admin\SubscriberController::class, 'export'])->name('subscribers.export');
            Route::apiResource('subscribers', Admin\SubscriberController::class)->only(['index', 'update', 'destroy'])->parameters(['subscribers' => 'id'])->whereNumber('id');

            // Admins only
            Route::middleware('admin')->group(function () {
                Route::get('/settings', [Admin\SettingsController::class, 'show'])->name('settings.show');
                Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
                Route::post('/users/{id}/invite', [Admin\UserController::class, 'invite'])->whereNumber('id')->middleware('throttle:forms')->name('users.invite');
                Route::apiResource('users', Admin\UserController::class)->except('show')->parameters(['users' => 'id'])->whereNumber('id');
            });
        });
    });
});
