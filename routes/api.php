<?php

use App\Core\Helpers\StringHelpers;
use App\Core\Http\Controllers\ModuleController;
use App\Modules\Security\Http\Controllers\AuthController;
use App\Modules\Security\Http\Controllers\PermissionController;
use App\Modules\Security\Http\Controllers\RoleController;
use App\Modules\Security\Http\Controllers\UserController;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;

Route::post('security/auth/login', [AuthController::class, 'login']);
Route::post('security/auth/token', [AuthController::class, 'token']);

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('core')->group(function() {
        Route::resource('modules', ModuleController::class);
    });

    Route::prefix('security')->group(function() {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::resource('users', UserController::class);

        Route::get('roles/lookup', [RoleController::class, 'lookup']);
        Route::resource('roles', RoleController::class);
        Route::patch('roles/{role}/permissions', [RoleController::class, 'definePermissions']);
        
        Route::resource('permissions', PermissionController::class);
    });
});
Route::get('test', function() {
    return StringHelpers::toStringLiteral(new Date());
});