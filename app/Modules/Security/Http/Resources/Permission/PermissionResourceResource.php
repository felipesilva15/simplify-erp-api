<?php

namespace App\Modules\Security\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PermissionResourceResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="Sample", minLength=1, maxLength=80),
 * )
 */
class PermissionResourceResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug
        ];
    }
}