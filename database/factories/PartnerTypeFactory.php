<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Partner\Models\PartnerType>
 */
class PartnerTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'code' => fake()->unique()->lexify('???')
        ];
    }
}
