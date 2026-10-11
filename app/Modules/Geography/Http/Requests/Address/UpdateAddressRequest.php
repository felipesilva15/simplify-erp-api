<?php

namespace App\Modules\Geography\Http\Requests\Address;

use App\Modules\Geography\Enums\AddressTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateAddressRequest",
 *     description="Payload de alteração de um endereço. O alvo polimórfico (addressable_type/addressable_id) é imutável e não pode ser enviado.",
 *     required={"type","country_id"},
 *
 *     @OA\Property(property="type", ref="#/components/schemas/AddressTypeEnum"),
 *     @OA\Property(property="is_primary", type="boolean", example=false),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="state_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="city_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="district", type="string", example="Centro", minLength=1, maxLength=80, nullable=true),
 *     @OA\Property(property="street", type="string", example="Rua das Flores", minLength=1, maxLength=120, nullable=true),
 *     @OA\Property(property="number", type="string", example="42", minLength=1, maxLength=16, nullable=true),
 *     @OA\Property(property="state_name", type="string", example="California", minLength=1, maxLength=80, nullable=true),
 *     @OA\Property(property="city_name", type="string", example="Sacramento", minLength=1, maxLength=90, nullable=true),
 *     @OA\Property(property="address_line_1", type="string", example="2201 K Street", minLength=1, maxLength=160, nullable=true),
 *     @OA\Property(property="address_line_2", type="string", example="Suite 200", minLength=1, maxLength=160, nullable=true),
 *     @OA\Property(property="postal_code", type="string", example="95814", minLength=1, maxLength=20, nullable=true),
 *     @OA\Property(property="complement", type="string", example="Sala 101", minLength=1, maxLength=40, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Entregar somente em horário comercial", nullable=true)
 * )
 */
class UpdateAddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'addressable_type' => 'prohibited',
            'addressable_id' => 'prohibited',
            'type' => ['required', Rule::enum(AddressTypeEnum::class)],
            'is_primary' => 'nullable|boolean',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'district' => 'nullable|string|min:1|max:80',
            'street' => 'nullable|string|min:1|max:120',
            'number' => 'nullable|string|min:1|max:16',
            'state_name' => 'nullable|string|min:1|max:80',
            'city_name' => 'nullable|string|min:1|max:90',
            'address_line_1' => 'nullable|string|min:1|max:160',
            'address_line_2' => 'nullable|string|min:1|max:160',
            'postal_code' => 'nullable|string|min:1|max:20',
            'complement' => 'nullable|string|min:1|max:40',
            'notes' => 'nullable|string',
        ];
    }
}
