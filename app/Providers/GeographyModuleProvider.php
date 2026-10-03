<?php

namespace App\Providers;

use App\Modules\Geography\Repositories\Eloquent\CityRepository;
use App\Modules\Geography\Repositories\Eloquent\CountryRepository;
use App\Modules\Geography\Repositories\Eloquent\StateRepository;
use App\Modules\Geography\Repositories\Interfaces\CityRepositoryInterface;
use App\Modules\Geography\Repositories\Interfaces\CountryRepositoryInterface;
use App\Modules\Geography\Repositories\Interfaces\StateRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class GeographyModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
    }
}