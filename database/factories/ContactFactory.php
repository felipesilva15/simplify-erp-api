<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Partner\Models\Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'partner_id' => fake()->numberBetween(0, 100),
            'name' => fake()->name(),
            'department' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->numerify('###########'),
            'phone' => fake()->numerify('##########'),
            'main' => fake()->boolean(),
            'notes' => fake()->text()
        ];
    }
}
