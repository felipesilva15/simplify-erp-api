<?php

namespace App\Modules\Security\Http\Resources\Module;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ModuleResourceResource",
 *
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Usuários", minLength=1, maxLength=80),
 *      @OA\Property(property="label", type="string", example="Usuários", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="users", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Recursos de usuários", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/ModuleResourcePermissionResource")),
 * )
 */
class ModuleResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'permissions' => ModuleResourcePermissionResource::collection($this->permissions),
        ];
    }
}
