<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Helpers\ListHelpers;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserService extends BaseCrudService
{
    public function __construct(UserRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function afterStore(Model $user, mixed $data): Model
    {
        return $this->defineRoles($user, $this->roleIds($data))->data;
    }

    protected function afterUpdate(Model $user, mixed $data): Model
    {
        return $this->defineRoles($user, $this->roleIds($data))->data;
    }

    public function defineRoles(User $user, array $roleIds = []): ServiceResult
    {
        return new ServiceResult(
            data: $this->repository->sync($user, 'roles', $roleIds)
        );
    }

    private function roleIds(mixed $data): array
    {
        return ListHelpers::groupListByProperty((array) $this->dataValue($data, 'roles', []), 'id');
    }
}
