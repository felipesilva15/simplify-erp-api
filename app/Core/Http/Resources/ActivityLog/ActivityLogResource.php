<?php

namespace App\Core\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ActivityLogResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="origin_type", type="string", example="user"),
 *     @OA\Property(property="origin_id", type="string", example="1"),
 *     @OA\Property(property="action", ref="#/components/schemas/ActivityActionEnum"),
 *     @OA\Property(property="action_label", type="string", example="Criado"),
 *     @OA\Property(property="description", type="string", example="Registro criado", nullable=true),
 *     @OA\Property(property="route_name", type="string", example="users.store", nullable=true),
 *     @OA\Property(property="route_path", type="string", example="api/security/users", nullable=true),
 *     @OA\Property(property="ip_address", type="string", example="127.0.0.1", nullable=true),
 *     @OA\Property(property="user_agent", type="string", example="Mozilla/5.0", nullable=true),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Felipe Silva")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true)
 * )
 */
class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'origin_type' => $this->origin_type,
            'origin_id' => $this->origin_id,
            'action' => $this->action,
            'action_label' => $this->action_label,
            'description' => $this->description,
            'route_name' => $this->route_name,
            'route_path' => $this->route_path,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username
            ]),
            'created_at' => $this->created_at,
        ];
    }
}