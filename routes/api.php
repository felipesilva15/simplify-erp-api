<?php

use App\Core\Http\Controllers\ModuleController;
use App\Modules\Auth\Http\Controllers\AuthController;
use App\Modules\Auth\Http\Controllers\PermissionController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('core')->group(function() {
        Route::apiResource('modules', ModuleController::class);
    });

    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::apiResource('users', UserController::class);

        Route::apiResource('roles', RoleController::class);
        Route::patch('roles/{role}/permissions', [RoleController::class, 'definePermissions']);
        
        Route::apiResource('permissions', PermissionController::class);
    });

    Route::get('test', function(AuthService $authService) {
        // return response()->json($authService->getLoggedInUser()->permissions()->get(), 200);
        return response()->json($authService->getLoggedInUser()->can('permissions.view'), 200);
    });
});