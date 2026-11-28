<?php

namespace App\Providers;

use App\Modules\Inventory\Repositories\Eloquent\WarehouseRepository;
use App\Modules\Inventory\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class InventoryModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
    }
}