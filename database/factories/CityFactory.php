<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Core\Models\City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'state_id' => fake()->numberBetween(0, 100),
            'name' => fake()->name(),
            'ibge_code' => fake()->unique()->lexify('???????')
        ];
    }
}
