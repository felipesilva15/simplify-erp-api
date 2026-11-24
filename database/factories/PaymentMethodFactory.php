<?php

namespace Database\Factories;

use App\Modules\Finance\Enums\FinanceApplicabilityEnum;
use App\Modules\Finance\Enums\PaymentTypeEnum;
use App\Modules\Finance\Enums\TaxTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Finance\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'condition' => fake()->name(),
            'payment_type' => PaymentTypeEnum::BankDeposit,
            'applicable_type' => FinanceApplicabilityEnum::Both,
            'tax_type' => TaxTypeEnum::FiexedValueAndAliquot,
            'tax_aliquot' => fake()->randomFloat(2, 0, 9999),
            'tax_fixed_value' => fake()->randomFloat(2, 0, 9999),
            'penalty_aliquot' => fake()->randomFloat(2, 0, 9999),
            'interest_aliquot' => fake()->randomFloat(2, 0, 9999),
            'payment_deadline' => fake()->numberBetween(0, 100)
        ];
    }
}
