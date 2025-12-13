<?php

namespace App\Providers;

use App\Modules\Auth\Repositories\Eloquent\PermissionRepository;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Auth\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Modules\Auth\Repositories\Interfaces\RoleRepositoryInterface;
use App\Modules\Auth\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AuthModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
