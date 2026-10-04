<?php

namespace App\Providers;

use App\Core\Repositories\Eloquent\ActivityLogRepository;
use App\Core\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppCoreProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
    }
}
