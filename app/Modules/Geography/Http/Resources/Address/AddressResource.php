<?php

namespace App\Modules\Geography\Http\Resources\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="AddressResource",
 *
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="addressable_type", type="string", example="partner"),
 *      @OA\Property(property="addressable_id", type="integer", example=1),
 *      @OA\Property(property="type", type="string", example="BILLING"),
 *      @OA\Property(property="is_primary", type="boolean", example=false),
 *      @OA\Property(property="country_id", type="integer", example=1),
 *      @OA\Property(property="country_iso_code", type="string", example="BR"),
 *      @OA\Property(property="state_id", type="integer", example=1, nullable=true),
 *      @OA\Property(property="city_id", type="integer", example=1, nullable=true),
 *      @OA\Property(property="district", type="string", example="Centro", nullable=true),
 *      @OA\Property(property="street", type="string", example="Rua das Flores", nullable=true),
 *      @OA\Property(property="number", type="string", example="42", nullable=true),
 *      @OA\Property(property="state_name", type="string", example="California", nullable=true),
 *      @OA\Property(property="city_name", type="string", example="Sacramento", nullable=true),
 *      @OA\Property(property="address_line_1", type="string", example="2201 K Street", nullable=true),
 *      @OA\Property(property="address_line_2", type="string", example="Suite 200", nullable=true),
 *      @OA\Property(property="postal_code", type="string", example="95814", nullable=true),
 *      @OA\Property(property="complement", type="string", example="Sala 101", nullable=true),
 *      @OA\Property(property="notes", type="string", example="Entregar somente em horário comercial", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true)
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'addressable_type' => $this->addressable_type,
            'addressable_id' => $this->addressable_id,
            'type' => $this->type?->value,
            'is_primary' => $this->is_primary,
            'country_id' => $this->country_id,
            'country_iso_code' => $this?->country->iso_code,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'district' => $this->district,
            'street' => $this->street,
            'number' => $this->number,
            'state_name' => $this->state_name,
            'city_name' => $this->city_name,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'postal_code' => $this->postal_code,
            'complement' => $this->complement,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
