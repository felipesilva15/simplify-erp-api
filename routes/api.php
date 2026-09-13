<?php

use App\Core\Http\Controllers\ModuleController;
use App\Core\Http\Controllers\ResourceController;
use App\Core\Models\ActivityLog;
use App\Modules\Partner\Http\Controllers\PartnerController;
use App\Modules\Partner\Http\Controllers\PartnerTypeController;
use App\Modules\Security\Http\Controllers\AuthController;
use App\Modules\Security\Http\Controllers\PermissionController;
use App\Modules\Security\Http\Controllers\RoleController;
use App\Modules\Security\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('security/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('security/auth/token', [AuthController::class, 'token'])->name('auth.token');

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('core')->group(function() {
        Route::crudResource('modules', ModuleController::class);
        Route::crudResource('resources', ResourceController::class);
    });

    Route::prefix('security')->group(function() {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('users/lookup', [UserController::class, 'lookup'])->name('users.lookup');
        Route::crudResource('users', UserController::class);

        Route::get('roles/lookup', [RoleController::class, 'lookup'])->name('roles.lookup');
        Route::crudResource('roles', RoleController::class);
        Route::patch('roles/{role}/permissions', [RoleController::class, 'definePermissions'])->name('roles.definePermissions');
        
        Route::crudResource('permissions', PermissionController::class);
    });

    Route::prefix('partner')->group(function() {
        Route::get('partner-types/lookup', [PartnerTypeController::class, 'lookup'])->name('partner-types.lookup');
        Route::crudResource('partner-types', PartnerTypeController::class);

        Route::get('partners/lookup', [PartnerController::class, 'lookup'])->name('partners.lookup');
        Route::crudResource('partners', PartnerController::class);
    });
});
Route::get('test', function() {
    return ActivityLog::orderByDesc('created_at')->get();
});