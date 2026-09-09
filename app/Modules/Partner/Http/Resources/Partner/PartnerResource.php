<?php

namespace App\Modules\Partner\Http\Resources\Partner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="partner_type_code", type="string", example="Sample", minLength=1, maxLength=20),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="trade_name", type="string", example="Sample", minLength=1, maxLength=150),
 *      @OA\Property(property="person_type", type="string", example="Sample", minLength=1, maxLength=30),
 *      @OA\Property(property="taxpayer_type", type="string", example="Sample", minLength=1, maxLength=30),
 *      @OA\Property(property="document_number", type="string", example="Sample", minLength=1, maxLength=20),
 *      @OA\Property(property="identity_number", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="identity_issuer", type="string", example="Sample", minLength=1, maxLength=20, nullable=true),
 *      @OA\Property(property="partner_since", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="state_registration", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *      @OA\Property(property="municipal_registration", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="suframa_registration", type="string", example="Sample", minLength=1, maxLength=9, nullable=true),
 *      @OA\Property(property="marital_status", type="string", example="Sample", minLength=1, maxLength=30, nullable=true),
 *      @OA\Property(property="cbo", type="string", example="Sample", minLength=1, maxLength=6, nullable=true),
 *      @OA\Property(property="gender", type="string", example="Sample", minLength=1, maxLength=12, nullable=true),
 *      @OA\Property(property="birth_date", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="father_name", type="string", example="Sample", minLength=1, maxLength=120, nullable=true),
 *      @OA\Property(property="father_document", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="mother_name", type="string", example="Sample", minLength=1, maxLength=120, nullable=true),
 *      @OA\Property(property="mother_document", type="string", example="Sample", minLength=1, maxLength=15, nullable=true),
 *      @OA\Property(property="pix_type", type="string", example="Sample", minLength=1, maxLength=30, nullable=true),
 *      @OA\Property(property="pix_key", type="string", example="Sample", minLength=1, maxLength=0, nullable=true),
 *      @OA\Property(property="notes", type="string", example="Sample", minLength=1, maxLength=0, nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-09T02:22:32.660898Z", nullable=true)
 * )
 */
class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'partner_type_code' => $this->partner_type_code,
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
            'cbo' => $this->cbo,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'father_name' => $this->father_name,
            'father_document' => $this->father_document,
            'mother_name' => $this->mother_name,
            'mother_document' => $this->mother_document,
            'pix_type' => $this->pix_type,
            'pix_key' => $this->pix_key,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}