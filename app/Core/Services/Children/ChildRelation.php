<?php

namespace App\Core\Services\Children;

use App\Core\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

final class ChildRelation
{
    public function __construct(
        public readonly string $relation,
        public readonly string $model,
        public readonly string $sync,
    ) { }

    public function resolve(Model $header, ActivityLogService $activity): BaseChildSync
    {
        return app($this->sync, [
            'header' => $header,
            'definition' => $this,
            'activity' => $activity,
        ]);
    }
}
