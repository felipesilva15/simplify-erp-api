<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Core\Models\State>
 */
class StateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country_id' => fake()->numberBetween(0, 100),
            'name' => fake()->name(),
            'uf' => fake()->unique()->lexify('??'),
            'ibge_code' => fake()->unique()->numerify('##')
        ];
    }
}
