<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Core\Models\Country>
 */
class CountryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'iso_code' => fake()->unique()->lexify('??'),
            'name' => fake()->name()
        ];
    }
}
