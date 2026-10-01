<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\HR\Models\Profession>
 */
class ProfessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cbo' => fake()->unique()->numerify('######'),
            'name' => fake()->name()
        ];
    }
}
