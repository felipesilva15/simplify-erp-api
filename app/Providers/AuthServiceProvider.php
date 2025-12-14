<?php

namespace App\Providers;

use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {   
        $this->registerPolicies();

        // Gate::before(function ($user, $ability) {
        //     return $user->is_admin ? true : null;
        // });
    }
}
