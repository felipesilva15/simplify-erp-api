<?php

namespace Database\Factories;
use App\Modules\Finance\Enums\FinanceApplicabilityEnum;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Finance\Models\FinanceCategory>
 */
class FinanceCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => fake()->boolean() ? null : fake()->numberBetween(0, 100),
            'code' => fake()->numerify('#####'),
            'name' => fake()->name(),
            'applicable_type' => fake()->randomElement(FinanceApplicabilityEnum::cases())
        ];
    }
}
