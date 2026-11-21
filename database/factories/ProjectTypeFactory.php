<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Construction\Models\ProjectType>
 */
class ProjectTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name()
        ];
    }
}
