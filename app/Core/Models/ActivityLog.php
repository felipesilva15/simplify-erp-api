<?php

namespace App\Core\Models;

use App\Modules\Security\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Core\Enums\ActivityActionEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'origin_type',
        'origin_id',
        'action',
        'user_id',
        'description',
        'route_name',
        'route_path',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'action' => ActivityActionEnum::class,
    ];

    protected $appends = ['origin_label', 'action_label'];

    public function origin(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'origin_type', 'origin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function originLabel(): Attribute
    {
        return Attribute::get(function () {
            $class = $this->origin_type;

            return class_exists($class) && method_exists($class, 'activityLabel')
                ? $class::activityLabel()
                : $this->origin_type;
        });
    }

    protected function actionLabel(): Attribute
    {
        return Attribute::get(fn () => $this->action->label());
    }
}