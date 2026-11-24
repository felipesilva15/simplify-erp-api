<?php

namespace App\Modules\Finance\DTO;

use Carbon\Carbon;

class PaymentMethodDTO
{
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public string $condition = '',
        public string $payment_type = '',
        public string $applicable_type = '',
        public ?string $tax_type = '',
        public ?string $tax_aliquot = '',
        public ?string $tax_fixed_value = '',
        public ?string $penalty_aliquot = '',
        public ?string $interest_aliquot = '',
        public int $payment_deadline = 0,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            condition: $data['condition'] ?? '',
            payment_type: $data['payment_type'] ?? '',
            applicable_type: $data['applicable_type'] ?? '',
            tax_type: $data['tax_type'] ?? null,
            tax_aliquot: $data['tax_aliquot'] ?? null,
            tax_fixed_value: $data['tax_fixed_value'] ?? null,
            penalty_aliquot: $data['penalty_aliquot'] ?? null,
            interest_aliquot: $data['interest_aliquot'] ?? null,
            payment_deadline: $data['payment_deadline'] ?? 0,
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'condition' => $this->condition,
            'payment_type' => $this->payment_type,
            'applicable_type' => $this->applicable_type,
            'tax_type' => $this->tax_type,
            'tax_aliquot' => $this->tax_aliquot,
            'tax_fixed_value' => $this->tax_fixed_value,
            'penalty_aliquot' => $this->penalty_aliquot,
            'interest_aliquot' => $this->interest_aliquot,
            'payment_deadline' => $this->payment_deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}