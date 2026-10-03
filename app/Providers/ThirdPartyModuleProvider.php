<?php

namespace App\Providers;

use App\Modules\ThirdParty\Repositories\Eloquent\PartnerRepository;
use App\Modules\ThirdParty\Repositories\Eloquent\PartnerTypeRepository;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerRepositoryInterface;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerTypeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ThirdPartyModuleProvider extends ServiceProvider
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
