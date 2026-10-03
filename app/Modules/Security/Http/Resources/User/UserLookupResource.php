<?php

namespace App\Modules\Security\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="UserLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Felipe Silva", minLength=1, maxLength=180),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1 | Email: felipe@example.com", minLength=1, maxLength=180),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Felipe Silva", minLength=1, maxLength=180),
 *          @OA\Property(property="email", type="string", example="felipe@example.com", minLength=1, maxLength=180, nullable=true),
 *          @OA\Property(property="username", type="string", example="felipe", minLength=1, maxLength=50, nullable=true),
 *          @OA\Property(property="phone_number", type="string", example="+55 11 99999-9999", minLength=1, maxLength=20, nullable=true)
 *      )
 * )
 */
class UserLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => 'Cod.: '.$this->id.' | Email: '.$this->email,
            'meta' => $this->only('id', 'name', 'email', 'username', 'phone_number')
        ];
    }
}