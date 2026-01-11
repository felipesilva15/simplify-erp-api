<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Security\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;
    protected string $defaultPassoword = 'Password@123';

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make($this->getDefaultPassword()),
            'username' => fake()->userName(),
            'phone_number' => fake()->numerify('###########'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
        ];
    }

    public function getDefaultPassword(): string {
        return $this->defaultPassoword;
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
