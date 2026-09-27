<?php

namespace App\Providers;

use App\Modules\Partner\Repositories\Eloquent\PartnerRepository;
use App\Modules\Partner\Repositories\Eloquent\PartnerTypeRepository;
use App\Modules\Partner\Repositories\Interfaces\PartnerRepositoryInterface;
use App\Modules\Partner\Repositories\Interfaces\PartnerTypeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class PartnerModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PartnerTypeRepositoryInterface::class, PartnerTypeRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
    }
}
