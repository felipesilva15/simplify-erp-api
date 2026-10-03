<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::macro('crudResource', function (string $name, string $controller) {
            Route::resource($name, $controller);
            Route::get("{$name}/{id}/activity-logs", [$controller, 'activityLogs']);
        });
    }
}
