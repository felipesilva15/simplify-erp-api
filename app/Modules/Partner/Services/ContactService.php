<?php

namespace App\Modules\Partner\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Partner\Repositories\Interfaces\ContactRepositoryInterface;

class ContactService extends BaseCrudService
{
    public function __construct(ContactRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}