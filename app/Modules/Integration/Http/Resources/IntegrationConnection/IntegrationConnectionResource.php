<?php

namespace App\Modules\Integration\Http\Resources\IntegrationConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="IntegrationConnectionResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="uuid", type="string"),
 *     @OA\Property(property="provider", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="has_credentials", type="boolean"),
 *     @OA\Property(property="settings", type="object", nullable=true),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="last_used_at", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", nullable=true),
 *     @OA\Property(property="updated_at", type="string", nullable=true)
 * )
 */
class IntegrationConnectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'provider' => $this->provider,
            'name' => $this->name,
            'has_credentials' => !empty($this->credentials),
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'last_used_at' => $this->last_used_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
