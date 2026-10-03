<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class BaseModel extends Model
{
    protected static ?string $activityLabel = null;
    protected static ?string $morphAlias = null;


    public static function activityLabel(): string
    {
        return static::$activityLabel ?? Str::headline(class_basename(static::class));
    }

    public static function morphAlias(): string
    {
        return static::$morphAlias ?? Str::kebab(class_basename(static::class));
    }
}