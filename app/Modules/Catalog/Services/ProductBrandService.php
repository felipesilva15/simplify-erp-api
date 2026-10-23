<?php

namespace App\Modules\Catalog\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Catalog\Repositories\Interfaces\ProductBrandRepositoryInterface;

class ProductBrandService extends BaseCrudService
{
    public function __construct(ProductBrandRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}