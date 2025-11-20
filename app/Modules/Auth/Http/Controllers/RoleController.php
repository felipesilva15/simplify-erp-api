<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Http\Resources\RoleResource;

class RoleController
{
    public function index(RoleService $service)
    {
        return RoleResource::collection($service->list());
    }
}