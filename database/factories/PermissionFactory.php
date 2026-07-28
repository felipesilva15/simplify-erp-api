<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Security\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->word(),
            'action' => fake()->word(),
            'description' => fake()->text(512)
        ];
    }

    public function withName(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->word().'.'.$attributes['action'],
        ]);
    }
}
