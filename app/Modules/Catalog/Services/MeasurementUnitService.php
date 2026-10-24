<?php

namespace App\Modules\Catalog\Services;

use App\Core\Exceptions\BusinessRuleException;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Catalog\Repositories\Interfaces\MeasurementUnitRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnitService extends BaseCrudService
{
    public function __construct(MeasurementUnitRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function beforeUpdate(Model $entity, mixed $data): void
    {
        $baseUnitId = $this->dataValue($data, 'base_unit_id');

        if ($entity->is_system) {
            throw new BusinessRuleException(
                message: 'Não é possível atualizar uma unidade de medida do sistema.',
                errors: ['is_system' => 'Unidades de medida do sistema não podem ser atualizadas.']
            );
        }

        if ($baseUnitId !== null && (int) $baseUnitId === (int) $entity->getKey()) {
            throw new BusinessRuleException(
                message: 'A unidade de medida não pode ser unidade base dela mesma.',
                errors: ['base_unit_id' => 'Selecione uma unidade de medida diferente da atual.']
            );
        }
    }

    protected function beforeDelete(Model $entity): void
    {
        if ($entity->is_system) {
            throw new BusinessRuleException(
                message: 'Não é possível excluir uma unidade de medida do sistema.',
                errors: ['is_system' => 'Unidades de medida do sistema não podem ser excluídas.']
            );
        }
    }
}
