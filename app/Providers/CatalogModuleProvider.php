<?php

namespace App\Providers;

use App\Modules\Catalog\Repositories\Eloquent\ProductCategoryRepository;
use App\Modules\Catalog\Repositories\Interfaces\ProductCategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class CatalogModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
    }
}