<?php

namespace App\Modules\Integration;

use App\Modules\Integration\Contracts\IConnectionResolver;
use App\Modules\Integration\Registry\ProviderRegistry;
use App\Modules\Integration\Services\SingleConnectionResolver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/integrations.php', 'integrations');

        $this->app->singleton(ProviderRegistry::class, function () {
            return new ProviderRegistry();
        });

        $this->app->bind(IConnectionResolver::class, SingleConnectionResolver::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Integration\Console\Commands\PruneIntegrationLogsCommand::class,
            ]);
        }
    }
}
