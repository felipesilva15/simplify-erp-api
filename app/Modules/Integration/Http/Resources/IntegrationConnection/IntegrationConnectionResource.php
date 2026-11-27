<?php

namespace App\Modules\Integration\Http\Resources\IntegrationConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="IntegrationConnectionResource",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="uuid", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="provider", type="string", example="billing"),
 *     @OA\Property(property="name", type="string", example="Conexão ERP Produção"),
 *     @OA\Property(property="has_credentials", type="boolean", example=true),
 *     @OA\Property(property="settings", type="object", nullable=true, example={"base_url":"https://api.exemplo.com"}),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="last_used_at", type="string", format="date-time", example="2026-05-20T14:30:00Z", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-20T14:30:00Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-05-20T14:30:00Z", nullable=true)
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
            'has_credentials' => ! empty($this->credentials),
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'last_used_at' => $this->last_used_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
