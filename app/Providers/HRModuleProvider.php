<?php

namespace App\Providers;

use App\Modules\HR\Repositories\Eloquent\ProfessionRepository;
use App\Modules\HR\Repositories\Interfaces\ProfessionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class HRModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProfessionRepositoryInterface::class, ProfessionRepository::class);
    }
}