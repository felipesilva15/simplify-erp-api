<?php

namespace App\Modules\Partner\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Partner\Enums\PersonTypeEnum;
use App\Modules\Partner\Enums\TaxpayerTypeEnum;
use App\Modules\Partner\Repositories\Interfaces\PartnerRepositoryInterface;

class PartnerService extends BaseCrudService
{
    public function __construct(PartnerRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function prepareData(mixed $data): mixed {
        if ($data->person_type == PersonTypeEnum::Person->value)
            $data->taxpayer_type = TaxpayerTypeEnum::Exempt->value;

        return $data;
    } 
}