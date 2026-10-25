<?php

namespace App\Modules\Integration\Http\Resources\IntegrationConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
