<?php

namespace App\Providers;

use App\Modules\Auth\Repositories\Eloquent\PermitionRepository;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Auth\Repositories\Interfaces\PermitionRepositoryInterface;
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
        $this->app->bind(PermitionRepositoryInterface::class, PermitionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
