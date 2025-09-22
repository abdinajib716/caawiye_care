<?php

use App\Http\Controllers\Api\ActionLogController;
use App\Http\Controllers\Api\AiContentController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TermController;
use App\Http\Controllers\Api\UserController;
// Removed: BackendTermController import
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API endpoints
Route::get('/translations/{lang}', function (string $lang) {
    $path = resource_path("lang/{$lang}.json");

    if (! file_exists($path)) {
        return response()->json(['error' => 'Language not found'], 404);
    }

    $translations = json_decode(file_get_contents($path), true);

    return response()->json($translations);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/revoke-all', [AuthController::class, 'revokeAll']);
    });
});

// Protected API routes
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // User management
    Route::apiResource('users', UserController::class);
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('api.users.bulk-delete');

    // Role management
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/delete/bulk-delete', [RoleController::class, 'bulkDelete'])->name('api.roles.bulk-delete');

    // Permission management
    Route::get('permissions', [PermissionController::class, 'index'])->name('api.permissions.index');
    Route::get('permissions/groups', [PermissionController::class, 'groups'])->name('api.permissions.groups');
    Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('api.permissions.show');

    // Removed: Posts and Terms management routes

    // Settings management
    Route::get('settings', [SettingController::class, 'index'])->name('api.settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('api.settings.update');
    Route::get('settings/{key}', [SettingController::class, 'show'])->name('api.settings.show');

    // Action logs
    Route::get('action-logs', [ActionLogController::class, 'index'])->name('api.action-logs.index');
    Route::get('action-logs/{id}', [ActionLogController::class, 'show'])->name('api.action-logs.show');

    // AI Content Generation
    Route::prefix('ai')->group(function () {
        Route::get('providers', [AiContentController::class, 'getProviders'])->name('api.ai.providers');
        Route::post('generate-content', [AiContentController::class, 'generateContent'])->name('api.ai.generate-content');
    });

    // Removed: Module management routes
});

// Removed: Admin API routes for Terms
