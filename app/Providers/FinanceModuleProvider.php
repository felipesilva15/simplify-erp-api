<?php

namespace App\Providers;

use App\Modules\Finance\Repositories\Eloquent\PaymentMethodRepository;
use App\Modules\Finance\Repositories\Interfaces\PaymentMethodRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class FinanceModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
    }
}