<?php

namespace App\Modules\ThirdParty\Http\Resources\Partner;

use App\Modules\Geography\Http\Resources\Address\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerResource",
 *
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="partner_type_code", type="string", example="CTM", minLength=1, maxLength=20),
 *      @OA\Property(property="partner_type_name", type="string", example="Cliente", minLength=1, maxLength=80),
 *      @OA\Property(property="name", type="string", example="Simplify Comércio de Materiais Ltda.", minLength=1, maxLength=120),
 *      @OA\Property(property="trade_name", type="string", example="Simplify Materiais", minLength=1, maxLength=150),
 *      @OA\Property(property="person_type", ref="#/components/schemas/PersonTypeEnum"),
 *      @OA\Property(property="taxpayer_type", ref="#/components/schemas/TaxpayerTypeEnum"),
 *      @OA\Property(property="document_number", type="string", example="11222333000181", minLength=1, maxLength=20),
 *      @OA\Property(property="identity_number", type="string", example="123456789", maxLength=15, nullable=true),
 *      @OA\Property(property="identity_issuer", type="string", example="SSP/SP", maxLength=20, nullable=true),
 *      @OA\Property(property="partner_since", type="string", format="date", example="2020-03-15", nullable=true),
 *      @OA\Property(property="state_registration", type="string", example="123456789123", maxLength=14, nullable=true),
 *      @OA\Property(property="municipal_registration", type="string", example="12345678", maxLength=15, nullable=true),
 *      @OA\Property(property="suframa_registration", type="string", example="123456789", maxLength=9, nullable=true),
 *      @OA\Property(property="marital_status", ref="#/components/schemas/MaritalStatusEnum", nullable=true),
 *      @OA\Property(property="profession_id", type="integer", example=1, nullable=true),
 *      @OA\Property(property="profession", ref="#/components/schemas/PartnerProfessionResource"),
 *      @OA\Property(property="gender", ref="#/components/schemas/GenderEnum", nullable=true),
 *      @OA\Property(property="birth_date", type="string", format="date", example="1985-06-20", nullable=true),
 *      @OA\Property(property="father_name", type="string", example="José Almeida", maxLength=120, nullable=true),
 *      @OA\Property(property="father_document", type="string", example="12345678909", maxLength=15, nullable=true),
 *      @OA\Property(property="mother_name", type="string", example="Maria Almeida", maxLength=120, nullable=true),
 *      @OA\Property(property="mother_document", type="string", example="98765432100", maxLength=15, nullable=true),
 *      @OA\Property(property="pix_type", ref="#/components/schemas/PixTypeEnum", nullable=true),
 *      @OA\Property(property="pix_key", type="string", example="financeiro@simplify.com.br", nullable=true),
 *      @OA\Property(property="notes", type="string", example="Cliente desde 2020", nullable=true),
 *      @OA\Property(property="contacts", type="array", @OA\Items(ref="#/components/schemas/PartnerContactResource")),
 *      @OA\Property(property="addresses", type="array", @OA\Items(ref="#/components/schemas/AddressResource")),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true)
 * )
 */
class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'partner_type_code' => $this->partner_type_code,
            'partner_type_name' => $this->partnerType?->name,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'person_type' => $this->person_type,
            'taxpayer_type' => $this->taxpayer_type,
            'document_number' => $this->document_number,
            'identity_number' => $this->identity_number,
            'identity_issuer' => $this->identity_issuer,
            'partner_since' => $this->partner_since?->format('Y-m-d'),
            'state_registration' => $this->state_registration,
            'municipal_registration' => $this->municipal_registration,
            'suframa_registration' => $this->suframa_registration,
            'marital_status' => $this->marital_status,
            'profession_id' => $this->profession_id,
            'profession' => $this->whenLoaded('profession', function () {
                return new PartnerProfessionResource($this->profession);
            }),
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'father_name' => $this->father_name,
            'father_document' => $this->father_document,
            'mother_name' => $this->mother_name,
            'mother_document' => $this->mother_document,
            'pix_type' => $this->pix_type,
            'pix_key' => $this->pix_key,
            'notes' => $this->notes,
            'contacts' => PartnerContactResource::collection($this->contacts),
            'addresses' => AddressResource::collection($this->addresses),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
