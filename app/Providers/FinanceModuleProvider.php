<?php

namespace App\Providers;

use App\Modules\Finance\Repositories\Eloquent\PaymentMethodRepository;
use App\Modules\Finance\Repositories\Interfaces\PaymentMethodRepositoryInterface;
use App\Modules\Finance\Repositories\Eloquent\FinanceCategoryRepository;
use App\Modules\Finance\Repositories\Interfaces\FinanceCategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class FinanceModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(FinanceCategoryRepositoryInterface::class, FinanceCategoryRepository::class);
    }
}