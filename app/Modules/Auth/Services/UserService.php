<?php

namespace App\Modules\Auth\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Helpers\ListHelpers;
use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository) { }

    public function store(UserDTO $data): User {
        $user = $this->repository->store($data);
        $user = $this->defineRoles($user->id, ListHelpers::groupListByProperty($data->roles, 'id'));

        return $user;
    }

    public function edit(int $id): ?User {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    public function update(int $id, UserDTO $data): ?User {
        $user = $this->repository->update($id, $data);

        if (!$user) {
            throw new NotFoundHttpException();
        }
        
        $user = $this->defineRoles($id, ListHelpers::groupListByProperty($data->roles, 'id'));

        return $user;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?User {
        $user = $this->repository->findById($id);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    public function list(array $filters = []): LengthAwarePaginator {
        return $this->repository->list($filters);
    }

    public function defineRoles(int $id, array $roleIds = []): ?User {
        return $this->repository->syncRoles($id, $roleIds);
    }
}