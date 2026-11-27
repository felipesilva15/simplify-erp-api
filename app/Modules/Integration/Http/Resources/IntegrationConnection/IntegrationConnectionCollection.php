<?php

namespace App\Modules\Integration\Http\Resources\IntegrationConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="IntegrationConnectionCollection",
 *     type="object",
 *
 *     @OA\Property(property="data", type="array", example={{"id":1,"uuid":"550e8400-e29b-41d4-a716-446655440000","provider":"billing","name":"Conexão ERP Produção","has_credentials":true,"settings":{"base_url":"https://api.exemplo.com"},"is_active":true}}, @OA\Items(ref="#/components/schemas/IntegrationConnectionResource"))
 * )
 */
class IntegrationConnectionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'id' => $item->id,
                    'uuid' => $item->uuid,
                    'provider' => $item->provider,
                    'name' => $item->name,
                    'has_credentials' => ! empty($item->credentials),
                    'settings' => $item->settings,
                    'is_active' => $item->is_active,
                    'last_used_at' => $item->last_used_at,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })->all(),
        ];
    }
}
