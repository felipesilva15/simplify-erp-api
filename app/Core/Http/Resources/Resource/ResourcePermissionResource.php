<?php

namespace App\Core\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ResourcePermissionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="action", type="string", example="sample", minLength=1, maxLength=120),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 * )
 */
class ResourcePermissionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description
        ];
    }
}