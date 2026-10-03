<?php

namespace App\Providers;

use App\Core\Services\SwaggerGeneratorFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use L5Swagger\GeneratorFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeneratorFactory::class, SwaggerGeneratorFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(5)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols();
        });
    }
}
