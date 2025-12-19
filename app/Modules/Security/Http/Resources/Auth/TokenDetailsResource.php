<?php

namespace App\Modules\Security\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="TokenDetailsResource",
 *      @OA\Property(property="access_token", type="string", example="token123"),
 *      @OA\Property(property="token_type", type="string", example="bearer"),
 *      @OA\Property(property="expires_in", type="number", example=3600)
 * )
 */
class TokenDetailsResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'access_token' => $this->token,
            'token_type' => $this->type,
            'expires_in' => $this->expiresIn
        ];
    }
}