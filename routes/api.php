<?php

use App\Core\Http\Controllers\ModuleController;
use App\Modules\Auth\Http\Controllers\PermitionController;
use App\Modules\Auth\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('core')->group(function() {
    Route::apiResource('modules', ModuleController::class);
});

Route::prefix('auth')->group(function() {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permitions', PermitionController::class);
});