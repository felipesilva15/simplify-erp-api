<?php

namespace App\Modules\Auth\Http\Resources\Permition;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PermitionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="group", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="has_access_free", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="module", type="object", ref="#/components/schemas/PermitionModuleResource"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true)
 * )
 */
class PermitionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'action' => $this->action,
            'description' => $this->description,
            'has_access_free' => $this->has_access_free,
            'is_active' => $this->is_active,
            'module' => new PermitionModuleResource($this->module),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}