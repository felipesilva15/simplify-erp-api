<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermitionRole extends Pivot
{
    protected $table = 'permition_role';
}