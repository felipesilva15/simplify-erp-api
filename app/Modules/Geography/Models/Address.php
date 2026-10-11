<?php

namespace App\Modules\Geography\Models;

use App\Core\Models\BaseModel;
use App\Modules\Geography\Enums\AddressTypeEnum;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Address",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="addressable_type", type="string", example="partner"),
 *     @OA\Property(property="addressable_id", type="integer", example=1),
 *     @OA\Property(property="type", ref="#/components/schemas/AddressTypeEnum"),
 *     @OA\Property(property="is_primary", type="boolean", example=false),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="state_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="city_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="district", type="string", example="Centro", nullable=true),
 *     @OA\Property(property="street", type="string", example="Rua das Flores", nullable=true),
 *     @OA\Property(property="number", type="string", example="42", nullable=true),
 *     @OA\Property(property="state_name", type="string", example="California", nullable=true),
 *     @OA\Property(property="city_name", type="string", example="Sacramento", nullable=true),
 *     @OA\Property(property="address_line_1", type="string", example="2201 K Street", nullable=true),
 *     @OA\Property(property="address_line_2", type="string", example="Suite 200", nullable=true),
 *     @OA\Property(property="postal_code", type="string", example="95814", nullable=true),
 *     @OA\Property(property="complement", type="string", example="Sala 101", nullable=true),
 *     @OA\Property(property="notes", type="string", example="Entregar somente em horário comercial", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-10T02:22:32.660898Z", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="AddressItem",
 *     description="Item da lista addresses enviada junto do parceiro. O id ausente ou zero (0) indica um endereço novo; ausente no payload remove o endereço existente.",
 *
 *     @OA\Property(property="id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="type", ref="#/components/schemas/AddressTypeEnum"),
 *     @OA\Property(property="is_primary", type="boolean", example=false, nullable=true),
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
#[UseFactory(AddressFactory::class)]
class Address extends BaseModel
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'type',
        'is_primary',
        'country_id',
        'state_id',
        'city_id',
        'district',
        'street',
        'number',
        'state_name',
        'city_name',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'complement',
        'notes',
    ];

    protected $casts = [
        'type' => AddressTypeEnum::class,
        'is_primary' => 'boolean',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
