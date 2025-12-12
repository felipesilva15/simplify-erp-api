<?php

use App\Core\Http\Controllers\ModuleController;
use App\Modules\Auth\Http\Controllers\PermitionController;
use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('core')->group(function() {
    Route::apiResource('modules', ModuleController::class);
});

Route::prefix('auth')->group(function() {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::patch('roles/{id}/permitions', [RoleController::class, 'definePermitions']);
    Route::apiResource('permitions', PermitionController::class);
});