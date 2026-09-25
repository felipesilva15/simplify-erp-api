<?php

namespace App\Modules\Partner\Models;

use App\Core\Models\BaseModel;
use App\Modules\Partner\Enums\GenderEnum;
use App\Modules\Partner\Enums\MaritalStatusEnum;
use App\Modules\Partner\Enums\PersonTypeEnum;
use App\Modules\Partner\Enums\PixTypeEnum;
use App\Modules\Partner\Enums\TaxpayerTypeEnum;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Partner",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="partner_type_code", type="string", example="Sample"),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="trade_name", type="string", example="Sample"),
 *      @OA\Property(property="person_type", type="string", example="Sample"),
 *      @OA\Property(property="taxpayer_type", type="string", example="Sample"),
 *      @OA\Property(property="document_number", type="string", example="Sample"),
 *      @OA\Property(property="identity_number", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="identity_issuer", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="partner_since", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="state_registration", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="municipal_registration", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="suframa_registration", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="marital_status", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="cbo", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="gender", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="birth_date", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="father_name", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="father_document", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="mother_name", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="mother_document", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="pix_type", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="pix_key", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="notes", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true)
 * )
 */
#[UseFactory(PartnerFactory::class)]
class Partner extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use SoftDeletes, HasFactory;

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
        'cbo',
        'gender',
        'birth_date',
        'father_name',
        'father_document',
        'mother_name',
        'mother_document',
        'pix_type',
        'pix_key',
        'notes'
    ];

    protected $casts = [
        'partner_since' => 'date',
        'birth_date' => 'date',
        'gender' => GenderEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'person_type' => PersonTypeEnum::class,
        'pix_type' => PixTypeEnum::class,
        'taxpayer_type' => TaxpayerTypeEnum::class
    ];

    public function partnerType(): BelongsTo {
        return $this->belongsTo(PartnerType::class, 'partner_type_code', 'code');
    }

    public function contacts(): HasMany {
        return $this->hasMany(Contact::class);
    }
}