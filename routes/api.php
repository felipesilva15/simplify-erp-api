<?php

use App\Core\Http\Controllers\ModuleController;
use App\Modules\Auth\Http\Controllers\AuthController;
use App\Modules\Auth\Http\Controllers\PermitionController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\UserController;
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
        Route::patch('roles/{id}/permitions', [RoleController::class, 'definePermitions']);
        
        Route::apiResource('permitions', PermitionController::class);
    });
});

