<?php

namespace App\Modules\Catalog\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Catalog\Repositories\Interfaces\ProductCategoryRepositoryInterface;

class ProductCategoryService extends BaseCrudService
{
    public function __construct(ProductCategoryRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}