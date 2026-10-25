<?php

use App\Modules\Integration\Http\Controllers\IntegrationConnectionController;
use App\Modules\Integration\Http\Controllers\IntegrationProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::apiResource('integration-connections', IntegrationConnectionController::class);
        Route::get('integrations/providers', [IntegrationProviderController::class, 'index']);
    });
});
