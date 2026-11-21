<?php

namespace App\Providers;

use App\Modules\Construction\Repositories\Eloquent\ProjectTypeRepository;
use App\Modules\Construction\Repositories\Interfaces\ProjectTypeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ConstructionModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectTypeRepositoryInterface::class, ProjectTypeRepository::class);
    }
}