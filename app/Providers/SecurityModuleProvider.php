<?php

namespace App\Providers;

use App\Modules\Security\Repositories\Eloquent\PermissionRepository;
use App\Modules\Security\Repositories\Eloquent\RoleRepository;
use App\Modules\Security\Repositories\Eloquent\UserRepository;
use App\Modules\Security\Repositories\Eloquent\ModuleRepository;
use App\Modules\Security\Repositories\Eloquent\ResourceRepository;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use App\Modules\Security\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Modules\Security\Repositories\Interfaces\ResourceRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class SecurityModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(ResourceRepositoryInterface::class, ResourceRepository::class);
    }
}
