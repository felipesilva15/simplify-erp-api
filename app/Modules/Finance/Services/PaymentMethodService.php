<?php

namespace App\Modules\Finance\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Finance\Repositories\Interfaces\PaymentMethodRepositoryInterface;

class PaymentMethodService extends BaseCrudService
{
    public function __construct(PaymentMethodRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}