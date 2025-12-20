<?php

use App\Core\Http\Controllers\ModuleController;
use App\Modules\Security\Http\Controllers\AuthController;
use App\Modules\Security\Http\Controllers\PermissionController;
use App\Modules\Security\Http\Controllers\RoleController;
use App\Modules\Security\Http\Controllers\UserController;
use App\Modules\Security\Services\AuthService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::post('security/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('core')->group(function() {
        Route::resource('modules', ModuleController::class);
    });

    Route::prefix('security')->group(function() {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('users', UserController::class);

        Route::apiResource('roles', RoleController::class);
        Route::patch('roles/{role}/permissions', [RoleController::class, 'definePermissions']);
        
        Route::apiResource('permissions', PermissionController::class);
    });
});