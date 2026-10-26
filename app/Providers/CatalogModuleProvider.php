<?php

namespace App\Providers;

use App\Modules\Catalog\Repositories\Eloquent\ProductCategoryRepository;
use App\Modules\Catalog\Repositories\Interfaces\ProductCategoryRepositoryInterface;
use App\Modules\Catalog\Repositories\Eloquent\ProductBrandRepository;
use App\Modules\Catalog\Repositories\Interfaces\ProductBrandRepositoryInterface;
use App\Modules\Catalog\Repositories\Eloquent\MeasurementUnitRepository;
use App\Modules\Catalog\Repositories\Interfaces\MeasurementUnitRepositoryInterface;
use App\Modules\Catalog\Repositories\Eloquent\ProductRepository;
use App\Modules\Catalog\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class CatalogModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductBrandRepositoryInterface::class, ProductBrandRepository::class);
        $this->app->bind(MeasurementUnitRepositoryInterface::class, MeasurementUnitRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }
}