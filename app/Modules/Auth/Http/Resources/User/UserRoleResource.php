<?php

namespace App\Modules\Auth\Http\Resources\User;

use App\Modules\Auth\Http\Resources\Role\RolePermitionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="UserRoleResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80)
 * )
 */
class UserRoleResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}