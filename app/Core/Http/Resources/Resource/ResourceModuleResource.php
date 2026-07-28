<?php

namespace App\Core\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ResourceModuleResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="sample", minLength=1, maxLength=80),
 * )
 */
class ResourceModuleResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug
        ];
    }
}