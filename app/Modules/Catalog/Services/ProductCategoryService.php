<?php

namespace App\Modules\Catalog\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Catalog\Repositories\Interfaces\ProductCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryService extends BaseCrudService
{
    public function __construct(ProductCategoryRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function beforeUpdate(Model $entity, mixed $data): void
    {
        $parentId = $this->dataValue($data, 'parent_category_id');

        if ($parentId !== null && (int) $parentId === (int) $entity->getKey()) {
            throw new BusinessRuleException(
                message: 'A categoria não pode ser pai dela mesma.',
                errors: ['parent_category_id' => 'Selecione uma categoria diferente da atual.']
            );
        }
    }

    protected function beforeDelete(Model $entity): void
    {
        if (! $entity->is_active) {
            throw new BusinessRuleException(
                message: 'Não é possível excluir um registro inativo.',
                errors: ['id' => 'O registro já está inativo.']
            );
        }
    }
}
