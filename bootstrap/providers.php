<?php

use App\Providers\ThirdPartyModuleProvider;

return [
    App\Providers\AppCoreProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\MorphMapServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\SecurityModuleProvider::class,
    App\Providers\ThirdPartyModuleProvider::class,
    App\Providers\HRModuleProvider::class,
    App\Providers\GeographyModuleProvider::class,
    App\Providers\CatalogModuleProvider::class,
];
