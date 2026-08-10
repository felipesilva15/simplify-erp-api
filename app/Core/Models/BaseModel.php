<?php

namespace App\Core\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Str;

class BaseModel extends Model
{
    use LogsActivity;

    protected static ?string $activityLabel = null;

    public static function activityLabel(): string
    {
        return static::$activityLabel ?? Str::headline(class_basename(static::class));
    }
}