<?php

namespace App\Modules\Finance\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Finance\Repositories\Interfaces\FinanceCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class FinanceCategoryService extends BaseCrudService
{
    public function __construct(FinanceCategoryRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function beforeUpdate(Model $entity, mixed $data): void
    {
        $parentId = $this->dataValue($data, 'parent_id');

        if ($parentId !== null && (int) $parentId === (int) $entity->getKey()) {
            throw new BusinessRuleException(
                message: 'A categoria não pode ser pai dela mesma.',
                errors: ['parent_id' => 'Selecione uma categoria diferente da atual.']
            );
        }
    }
}