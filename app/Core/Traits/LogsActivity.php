<?php

namespace App\Core\Traits;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Models\ActivityLog;


trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (self $model) => $model->recordActivity(ActivityActionEnum::Created));
        static::updated(fn (self $model) => $model->recordActivity(ActivityActionEnum::Updated));
        static::deleted(fn (self $model) => $model->recordActivity(ActivityActionEnum::Deleted));
    }

    /** Override em model filha: protected bool $logsActivity = false; */
    protected bool $logsActivity = true;

    public function logActivity(ActivityActionEnum $action, ?string $description = null): void
    {
        $this->recordActivity($action, $description);
    }

    protected function recordActivity(ActivityActionEnum $action, ?string $description = null): void
    {
        if (! $this->logsActivity) {
            return;
        }

        ActivityLog::create([
            'origin_type'   => static::class,
            'origin_id'     => (string) $this->getKey(),
            'action'        => $action,
            'user_id'       => auth()->id(),
            'description'   => $description ?? $this->defaultActivityDescription($action),
            'route_name'    => request()?->route()->getName(),
            'route_path'    => request()?->path(),
            'ip_address'    => request()?->ip(),
            'user_agent'    => request()?->userAgent(),
        ]);
    }

    protected function defaultActivityDescription(ActivityActionEnum $action): string
    {
        return match ($action) {
            ActivityActionEnum::Created => 'Registro criado',
            ActivityActionEnum::Updated => 'Campos alterados: ' . implode(', ', array_keys($this->getChanges())),
            ActivityActionEnum::Deleted => 'Registro excluído',
            ActivityActionEnum::Approved => 'Registro aprovado',
            default => 'Campos alterados: ' . implode(', ', array_keys($this->getChanges())),
        };
    }
}