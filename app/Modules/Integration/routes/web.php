<?php

use App\Modules\Integration\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/{provider}/{connection?}', [WebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('integrations.webhook');
