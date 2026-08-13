<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class BaseModel extends Model
{
    protected static ?string $activityLabel = null;

    public static function activityLabel(): string
    {
        return static::$activityLabel ?? Str::headline(class_basename(static::class));
    }
}