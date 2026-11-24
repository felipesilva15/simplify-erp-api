<?php

namespace App\Modules\Finance\Enums;

/**
 * @OA\Schema(
 *   schema="FinanceApplicabilityEnum",
 *   type="string",
 *   description="Activity actions: CREDIT, DEBIT, BOTH",
 *   enum={"CREDIT", "DEBIT", "BOTH"}
 * )
 */
enum FinanceApplicabilityEnum: string
{
    case Credit = 'CREDIT';
    case Debit = 'DEBIT';
    case Both = 'BOTH';

    public function label(): string
    {
        return match ($this) {
            self::Credit => 'Crédito',
            self::Debit => 'Débito',
            self::Both => 'Ambos',
        };
    }
}
