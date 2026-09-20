<?php

namespace App\Modules\Partner\DTO;

use Carbon\Carbon;

class PartnerDTO
{
    public function __construct(
        public int $id = 0,
        public string $partner_type_code = '',
        public string $name = '',
        public string $trade_name = '',
        public string $person_type = '',
        public string $taxpayer_type = '',
        public string $document_number = '',
        public ?string $identity_number = '',
        public ?string $identity_issuer = '',
        public ?Carbon $partner_since = null,
        public ?string $state_registration = '',
        public ?string $municipal_registration = '',
        public ?string $suframa_registration = '',
        public ?string $marital_status = null,
        public ?string $cbo = null,
        public ?string $gender = null,
        public ?Carbon $birth_date = null,
        public ?string $father_name = '',
        public ?string $father_document = '',
        public ?string $mother_name = '',
        public ?string $mother_document = '',
        public ?string $pix_type = null,
        public ?string $pix_key = '',
        public ?string $notes = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            partner_type_code: $data['partner_type_code'] ?? '',
            name: $data['name'] ?? '',
            trade_name: $data['trade_name'] ?? '',
            person_type: $data['person_type'] ?? '',
            taxpayer_type: $data['taxpayer_type'] ?? '',
            document_number: $data['document_number'] ?? '',
            identity_number: $data['identity_number'] ?? '',
            identity_issuer: $data['identity_issuer'] ?? '',
            partner_since: isset($data['partner_since']) && $data['partner_since'] != null ? Carbon::parseFromLocale($data['partner_since']) : null,
            state_registration: $data['state_registration'] ?? '',
            municipal_registration: $data['municipal_registration'] ?? '',
            suframa_registration: $data['suframa_registration'] ?? '',
            marital_status: $data['marital_status'] ?? null,
            cbo: $data['cbo'] ?? null,
            gender: $data['gender'] ?? null,
            birth_date: isset($data['birth_date']) && $data['birth_date'] != null ? Carbon::parseFromLocale($data['birth_date']) : null,
            father_name: $data['father_name'] ?? '',
            father_document: $data['father_document'] ?? '',
            mother_name: $data['mother_name'] ?? '',
            mother_document: $data['mother_document'] ?? '',
            pix_type: $data['pix_type'] ?? null,
            pix_key: $data['pix_key'] ?? '',
            notes: $data['notes'] ?? '',
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }

    public function toArray(): array {
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
            'partner_since' => $this->partner_since,
            'state_registration' => $this->state_registration,
            'municipal_registration' => $this->municipal_registration,
            'suframa_registration' => $this->suframa_registration,
            'marital_status' => $this->marital_status,
            'cbo' => $this->cbo,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
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