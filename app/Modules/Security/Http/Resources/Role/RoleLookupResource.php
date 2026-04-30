<?php

namespace App\Modules\Security\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="RoleResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/RolePermissionResource")),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-01T11:21:13.562558Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-01T11:21:13.562558Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-01T11:21:13.562558Z", nullable=true)
 * )
 */
class RoleLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => 'Cod.: '.$this->id,
            'meta' => $this->only('id', 'name')
        ];
    }
}