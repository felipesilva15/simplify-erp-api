<?php

namespace App\Modules\Partner\Http\Requests\Partner;

use App\Modules\Partner\Enums\GenderEnum;
use App\Modules\Partner\Enums\MaritalStatusEnum;
use App\Modules\Partner\Enums\PersonTypeEnum;
use App\Modules\Partner\Enums\PixTypeEnum;
use App\Modules\Partner\Enums\TaxpayerTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      schema="StorePartnerRequest",
 *      required={"partner_type_code","name","trade_name","person_type","taxpayer_type","document_number"},
 *      @OA\Property(property="partner_type_code", type="string", example="Sample", minLength=1, maxLength=20),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="trade_name", type="string", example="Sample", minLength=1, maxLength=150),
 *      @OA\Property(property="person_type", ref="#/components/schemas/PersonTypeEnum"),
 *      @OA\Property(property="taxpayer_type", ref="#/components/schemas/TaxpayerTypeEnum"),
 *      @OA\Property(property="document_number", type="string", example="Sample", minLength=1, maxLength=20),
 *      @OA\Property(property="identity_number", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="identity_issuer", type="string", example="Sample", minLength=1, maxLength=20, nullable=true),
 *      @OA\Property(property="partner_since", type="string", format="date", example="2026-09-09", nullable=true),
 *      @OA\Property(property="state_registration", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *      @OA\Property(property="municipal_registration", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="suframa_registration", type="string", example="Sample", minLength=1, maxLength=9, nullable=true),
 *      @OA\Property(property="marital_status", ref="#/components/schemas/MaritalStatusEnum", nullable=true),
 *      @OA\Property(property="cbo", type="string", example="Sample", minLength=1, maxLength=6, nullable=true),
 *      @OA\Property(property="gender", ref="#/components/schemas/GenderEnum", nullable=true),
 *      @OA\Property(property="birth_date", type="string", format="date", example="2026-09-09", nullable=true),
 *      @OA\Property(property="father_name", type="string", example="Sample", minLength=1, maxLength=120, nullable=true),
 *      @OA\Property(property="father_document", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="mother_name", type="string", example="Sample", minLength=1, maxLength=120, nullable=true),
 *      @OA\Property(property="mother_document", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="pix_type", ref="#/components/schemas/PixTypeEnum", nullable=true),
 *      @OA\Property(property="pix_key", type="string", example="Sample", minLength=1, maxLength=0, nullable=true),
 *      @OA\Property(property="notes", type="string", example="Sample", minLength=1, maxLength=0, nullable=true)
 * )
 */
class StorePartnerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'partner_type_code' => 'required|string|min:1|max:20',
            'name' => 'required|string|min:1|max:120',
            'trade_name' => 'required|string|min:1|max:150',
            'person_type' => ['required', Rule::enum(PersonTypeEnum::class)],
            'taxpayer_type' => ['required', Rule::enum(TaxpayerTypeEnum::class)],
            'document_number' => 'required|string|min:1|max:20',
            'identity_number' => 'nullable|string|min:1|max:15',
            'identity_issuer' => 'nullable|string|min:1|max:20',
            'partner_since' => 'nullable|date',
            'state_registration' => 'nullable|string|min:1|max:14',
            'municipal_registration' => 'nullable|string|min:1|max:15',
            'suframa_registration' => 'nullable|string|min:1|max:9',
            'marital_status' => ['nullable', Rule::enum(MaritalStatusEnum::class)],
            'cbo' => 'nullable|string|min:1|max:6',
            'gender' => ['nullable', Rule::enum(GenderEnum::class)],
            'birth_date' => 'nullable|date',
            'father_name' => 'nullable|string|min:1|max:120',
            'father_document' => 'nullable|string|min:1|max:15',
            'mother_name' => 'nullable|string|min:1|max:120',
            'mother_document' => 'nullable|string|min:1|max:15',
            'pix_type' => ['nullable', Rule::enum(PixTypeEnum::class)],
            'pix_key' => 'nullable|string',
            'notes' => 'nullable|string'
        ];
    }
}