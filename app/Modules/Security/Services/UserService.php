<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Helpers\ListHelpers;
use App\Core\Services\BaseCrudService;
use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserService extends BaseCrudService
{
    public function __construct(UserRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function store(mixed $data): ServiceResult {
        $result = parent::store($data);
        $result->data = $this->defineRoles($result->data, ListHelpers::groupListByProperty($data->roles, 'id'))->data;

        return $result;
    }

    public function update(Model $user, mixed $data): ServiceResult {
        $result = parent::update($user, $data);
        $result->data = $this->defineRoles($result->data, ListHelpers::groupListByProperty($data->roles, 'id'))->data;

        return $result;
    }

    public function defineRoles(User $user, array $roleIds = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->sync($user, 'roles', $roleIds)
        );
    }
}