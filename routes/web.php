<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModeratorController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::prefix('admin')->name('admin.')->group(function () {

    // ---------- Guest ----------
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    // ---------- Authenticated (admin + moderator) ----------
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Forced/self-service password change — available even while
        // must_change_password is true, so no redirect loop occurs.
        Route::get('password', [AuthController::class, 'editPassword'])->name('password.edit');
        Route::put('password', [AuthController::class, 'updatePassword'])->name('password.update');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Orders: both roles can view/search/edit/change status.
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/export', [OrderController::class, 'exportCsv'])->name('orders.export');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        // Delete: allowed for admins; OrderController@destroy double-checks role too.
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('orders.destroy');

        // ---------- Admin only ----------
        Route::middleware('role:admin')->group(function () {
            Route::get('moderators', [ModeratorController::class, 'index'])->name('moderators.index');
            Route::post('moderators', [ModeratorController::class, 'store'])->name('moderators.store');
            Route::put('moderators/{user}', [ModeratorController::class, 'update'])->name('moderators.update');
            Route::post('moderators/{user}/reset-password', [ModeratorController::class, 'resetPassword'])->name('moderators.reset-password');
            Route::delete('moderators/{user}', [ModeratorController::class, 'destroy'])->name('moderators.destroy');

            Route::get('settings/general', [SettingsController::class, 'general'])->name('settings.general');
            Route::put('settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');

            Route::get('settings/telegram', [SettingsController::class, 'telegram'])->name('settings.telegram');
            Route::put('settings/telegram', [SettingsController::class, 'updateTelegram'])->name('settings.telegram.update');
            Route::post('settings/telegram/test', [SettingsController::class, 'testTelegram'])->name('settings.telegram.test');

            Route::get('settings/meta-pixel', [SettingsController::class, 'metaPixel'])->name('settings.pixel');
            Route::put('settings/meta-pixel', [SettingsController::class, 'updateMetaPixel'])->name('settings.pixel.update');

            Route::get('settings/meta-capi', [SettingsController::class, 'metaCapi'])->name('settings.capi');
            Route::put('settings/meta-capi', [SettingsController::class, 'updateMetaCapi'])->name('settings.capi.update');
            Route::post('settings/meta-capi/test', [SettingsController::class, 'testCapi'])->name('settings.capi.test');
        });
    });
});
