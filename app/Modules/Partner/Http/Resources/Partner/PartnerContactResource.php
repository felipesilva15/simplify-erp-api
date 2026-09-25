<?php

namespace App\Modules\Partner\Http\Resources\Partner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerContactResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Roberto", minLength=1, maxLength=120),
 *      @OA\Property(property="department", type="string", example="TI", minLength=1, maxLength=80),
 *      @OA\Property(property="email", format="email", type="string", example="roberto@email.com.br", minLength=1, maxLength=180),
 *      @OA\Property(property="mobile", type="string", example="11985984268", maxLength=11, nullable=true),
 *      @OA\Property(property="phone", type="string", example="1159856859", maxLength=10, nullable=true),
 *      @OA\Property(property="main", type="boolean", example=true),
 *      @OA\Property(property="notes", type="string", example="Entrar em contato somente para suporte", nullable=true)
 * )
 */
class PartnerContactResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'department' => $this->department,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'main' => $this->main,
            'notes' => $this->notes
        ];
    }
}