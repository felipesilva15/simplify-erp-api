<?php

namespace App\Providers;

use App\Modules\Auth\Repositories\Eloquent\ModuleRepository;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;
use App\Modules\Auth\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Modules\Auth\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AuthModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }
}
