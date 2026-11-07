<?php

namespace App\Modules\ThirdParty\Models;

use App\Core\Models\BaseModel;
use App\Modules\Geography\Models\Address;
use App\Modules\HR\Models\Profession;
use App\Modules\ThirdParty\Enums\GenderEnum;
use App\Modules\ThirdParty\Enums\MaritalStatusEnum;
use App\Modules\ThirdParty\Enums\PersonTypeEnum;
use App\Modules\ThirdParty\Enums\PixTypeEnum;
use App\Modules\ThirdParty\Enums\TaxpayerTypeEnum;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Partner",
 *
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="partner_type_code", type="string", example="CTM"),
 *      @OA\Property(property="name", type="string", example="Simplify Comércio de Materiais Ltda."),
 *      @OA\Property(property="trade_name", type="string", example="Simplify Materiais"),
 *      @OA\Property(property="person_type", ref="#/components/schemas/PersonTypeEnum"),
 *      @OA\Property(property="taxpayer_type", ref="#/components/schemas/TaxpayerTypeEnum"),
 *      @OA\Property(property="document_number", type="string", example="11222333000181"),
 *      @OA\Property(property="identity_number", type="string", example="123456789", nullable=true),
 *      @OA\Property(property="identity_issuer", type="string", example="SSP/SP", nullable=true),
 *      @OA\Property(property="partner_since", type="string", format="date", example="2020-03-15", nullable=true),
 *      @OA\Property(property="state_registration", type="string", example="123456789123", nullable=true),
 *      @OA\Property(property="municipal_registration", type="string", example="12345678", nullable=true),
 *      @OA\Property(property="suframa_registration", type="string", example="123456789", nullable=true),
 *      @OA\Property(property="marital_status", ref="#/components/schemas/MaritalStatusEnum", nullable=true),
 *      @OA\Property(property="profession_id", type="integer", example=1, nullable=true),
 *      @OA\Property(property="gender", ref="#/components/schemas/GenderEnum", nullable=true),
 *      @OA\Property(property="birth_date", type="string", format="date", example="1985-06-20", nullable=true),
 *      @OA\Property(property="father_name", type="string", example="José Almeida", nullable=true),
 *      @OA\Property(property="father_document", type="string", example="12345678909", nullable=true),
 *      @OA\Property(property="mother_name", type="string", example="Maria Almeida", nullable=true),
 *      @OA\Property(property="mother_document", type="string", example="98765432100", nullable=true),
 *      @OA\Property(property="pix_type", ref="#/components/schemas/PixTypeEnum", nullable=true),
 *      @OA\Property(property="pix_key", type="string", example="financeiro@simplify.com.br", nullable=true),
 *      @OA\Property(property="notes", type="string", example="Cliente desde 2020", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true)
 * )
 */
#[UseFactory(PartnerFactory::class)]
class Partner extends BaseModel
{
    /** @use HasFactory<PartnerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_type_code',
        'name',
        'trade_name',
        'person_type',
        'taxpayer_type',
        'document_number',
        'identity_number',
        'identity_issuer',
        'partner_since',
        'state_registration',
        'municipal_registration',
        'suframa_registration',
        'marital_status',
        'profession_id',
        'gender',
        'birth_date',
        'father_name',
        'father_document',
        'mother_name',
        'mother_document',
        'pix_type',
        'pix_key',
        'notes',
    ];

    protected $casts = [
        'partner_since' => 'date',
        'birth_date' => 'date',
        'gender' => GenderEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'person_type' => PersonTypeEnum::class,
        'pix_type' => PixTypeEnum::class,
        'taxpayer_type' => TaxpayerTypeEnum::class,
    ];

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function partnerType(): BelongsTo
    {
        return $this->belongsTo(PartnerType::class, 'partner_type_code', 'code');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
