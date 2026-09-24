<?php

namespace App\Providers;

use App\Core\Repositories\Eloquent\ActivityLogRepository;
use App\Core\Repositories\Eloquent\ModuleRepository;
use App\Core\Repositories\Eloquent\ResourceRepository;
use App\Core\Repositories\Interfaces\ActivityLogRepositoryInterface;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Core\Repositories\Interfaces\ResourceRepositoryInterface;
use App\Core\Repositories\Eloquent\CountryRepository;
use App\Core\Repositories\Interfaces\CountryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppCoreProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(ResourceRepositoryInterface::class, ResourceRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
    }
}
