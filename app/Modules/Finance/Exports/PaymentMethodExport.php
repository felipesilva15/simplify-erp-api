<?php

namespace App\Modules\Finance\Exports;

use App\Core\Exports\BaseExport;

class PaymentMethodExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Condition',
            'Applicable Type',
            'Tax Type',
            'Tax Aliquot',
            'Tax Fixed Value',
            'Payment Deadline'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->condition,
            $row->applicable_type,
            $row->tax_type,
            $row->tax_aliquot,
            $row->tax_fixed_value,
            $row->payment_deadline
        ];
    }
}