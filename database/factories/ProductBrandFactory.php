<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Catalog\Models\ProductBrand>
 */
class ProductBrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name()
        ];
    }
}
