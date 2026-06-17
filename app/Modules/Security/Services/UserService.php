<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Helpers\ListHelpers;
use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository) { }

    public function store(UserDTO $data): ServiceResult {
        $user = $this->repository->store($data);
        $user = $this->defineRoles($user, ListHelpers::groupListByProperty($data->roles, 'id'))->data;

        return new ServiceResult(
            data: $user
        );
    }

    public function edit(User $user): ServiceResult {
        return new ServiceResult(
            data: $user,
            meta: [
                'editable' => true
            ]
        );
    }

    public function update(User $user, UserDTO $data): ServiceResult {
        $user = $this->repository->update($user, $data);
        $user = $this->defineRoles($user, ListHelpers::groupListByProperty($data->roles, 'id'))->data;

        return new ServiceResult(
            data: $user
        );
    }

    public function delete(User $user): ServiceResult {
        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $this->repository->delete($user)
            ]
        );
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function show(User $user): ServiceResult {
        return new ServiceResult(
            data: $user
        );
    }

    public function defineRoles(User $user, array $roleIds = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->sync($user, 'roles', $roleIds)
        );
    }
}