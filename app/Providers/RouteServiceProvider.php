<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::macro('crudResource', function (string $name, string $controller) {
            $routeName = Str::camel($name);

            Route::resource($name, $controller)->names([
                'index' => "{$routeName}.index",
                'create' => "{$routeName}.create",
                'store' => "{$routeName}.store",
                'show' => "{$routeName}.show",
                'edit' => "{$routeName}.edit",
                'update' => "{$routeName}.update",
                'destroy' => "{$routeName}.destroy",
            ]);

            Route::get("{$name}/{id}/activity-logs", [$controller, 'activityLogs'])
                ->name("{$routeName}.activityLogs");
        });
    }
}
