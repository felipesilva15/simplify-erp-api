<?php

namespace App\Providers;

use App\Core\Repositories\Eloquent\ModuleRepository;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppCoreProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
    }
}
