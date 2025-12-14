<?php

namespace App\Modules\Auth\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="UserResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="email", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="email_verified_at", type="string", format="date-time", example="2025-12-11T11:13:04.516675Z", nullable=true),
 *      @OA\Property(property="username", type="string", example="Sample", minLength=1, maxLength=80, nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *      @OA\Property(property="is_admin", type="boolean", example=false),
 *      @OA\Property(property="roles", type="array", @OA\Items(ref="#/components/schemas/UserRoleResource")),
 *      @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="users.viewAny")),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-11T11:13:04.516675Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-11T11:13:04.516675Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-11T11:13:04.516675Z", nullable=true)
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'username' => $this->username,
            'phone_number' => $this->phone_number,
            'is_admin' => $this->is_admin,
            'roles' => UserRoleResource::collection($this->roles),
            'permissions' => $this->is_admin ? ['*'] : $this->permissionsList(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}