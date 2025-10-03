<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\ActionLogController;
use App\Http\Controllers\Backend\Auth\ScreenshotGeneratorLoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmailTestController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\TermController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\UserLoginAsController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Admin routes.
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);
    Route::delete('roles/delete/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulk-delete');

    // Permissions Routes.
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');

    // Services Routes.
    Route::resource('services', ServiceController::class);
    Route::delete('services/bulk-delete', [ServiceController::class, 'bulkDelete'])->name('services.bulk-delete');
    Route::patch('services/bulk-update-status', [ServiceController::class, 'bulkUpdateStatus'])->name('services.bulk-update-status');

    // Customers Routes.
    Route::resource('customers', App\Http\Controllers\Backend\CustomerController::class);
    Route::delete('customers/bulk-delete', [App\Http\Controllers\Backend\CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');
    Route::patch('customers/bulk-update-status', [App\Http\Controllers\Backend\CustomerController::class, 'bulkUpdateStatus'])->name('customers.bulk-update-status');

    // Service Categories Routes.
    Route::resource('service-categories', App\Http\Controllers\Backend\ServiceCategoryController::class);
    Route::delete('service-categories/bulk-delete', [App\Http\Controllers\Backend\ServiceCategoryController::class, 'bulkDelete'])->name('service-categories.bulk-delete');

    // Order Zone Route (Create orders)
    Route::get('order-zone', [App\Http\Controllers\Backend\OrderZoneController::class, 'index'])->name('order-zone.index');

    // Orders Routes (View/Manage orders)
    Route::get('orders', [App\Http\Controllers\Backend\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Backend\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Backend\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/cancel', [App\Http\Controllers\Backend\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('orders/{order}', [App\Http\Controllers\Backend\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::delete('orders/bulk-delete', [App\Http\Controllers\Backend\OrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
    Route::patch('orders/bulk-update-status', [App\Http\Controllers\Backend\OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update-status');

    // Transactions Routes (View/Manage payment transactions)
    Route::get('transactions', [App\Http\Controllers\Backend\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [App\Http\Controllers\Backend\TransactionController::class, 'show'])->name('transactions.show');

    // Removed: Modules Routes

    // Settings Routes.
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    // Email testing routes
    Route::post('/settings/test-smtp', [SettingController::class, 'testSmtpConnection'])->name('settings.test-smtp');
    Route::post('/settings/send-test-email', [SettingController::class, 'sendTestEmail'])->name('settings.send-test-email');

    // Translation Routes.
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');

    // Login as & Switch back.
    Route::resource('users', UserController::class);
    Route::delete('users/delete/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
    Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

    // Action Log Routes.
    Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');

    // Email Test Routes.
    Route::prefix('email')->name('email.')->group(function () {
        Route::post('/test', [EmailTestController::class, 'sendTestEmail'])->name('test');
        Route::post('/test-connection', [EmailTestController::class, 'testConnection'])->name('test-connection');
        Route::get('/config-status', [EmailTestController::class, 'getConfigurationStatus'])->name('config-status');
        Route::get('/current-config', [EmailTestController::class, 'getCurrentConfig'])->name('current-config');
    });

    // Removed: Posts/Pages Routes, Terms Routes, and Media Routes

    // Editor Upload Route.
    Route::post('/editor/upload', [App\Http\Controllers\Backend\EditorController::class, 'upload'])->name('editor.upload');

    // AI Content Generation Routes.
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/providers', [App\Http\Controllers\Backend\AiContentController::class, 'getProviders'])->name('providers');
        Route::post('/generate-content', [App\Http\Controllers\Backend\AiContentController::class, 'generateContent'])->name('generate-content');
    });
});

/**
 * Profile routes.
 */
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/update-additional', [ProfileController::class, 'updateAdditional'])->name('update.additional');
});

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/screenshot-login/{email}', [ScreenshotGeneratorLoginController::class, 'login'])->middleware('web')->name('screenshot.login');
Route::get('/demo-preview', fn () => view('demo.preview'))->name('demo.preview');
