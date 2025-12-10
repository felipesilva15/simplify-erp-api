<?php

namespace App\Modules\Auth\Http\Resources\Permition;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PermitionModuleResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 * )
 */
class PermitionModuleResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}