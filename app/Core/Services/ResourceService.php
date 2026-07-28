<?php

namespace App\Core\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Repositories\Interfaces\ResourceRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

class ResourceService extends BaseCrudService
{
    public function __construct(ResourceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}