<?php

namespace App\Policies;

use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermission('permissions.viewAny');
    }

    public function view(User $user, User $model)
    {
        return $user->hasPermission('permissions.view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('permissions.create');
    }

    public function update(User $user, User $model)
    {
        return $user->hasPermission('permissions.update');
    }

    public function delete(User $user, User $model)
    {
        return $user->hasPermission('permissions.delete');
    }

    public function restore(User $user, Permission $model): bool
    {
        return $user->hasPermission('permissions.restore');
    }

    public function forceDelete(User $user, Permission $model): bool
    {
        return $user->hasPermission('permissions.forceDelete');
    }
}
