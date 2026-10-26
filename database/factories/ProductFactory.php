<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Catalog\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->name(),
            'sku' => fake()->name(),
            'gtin' => fake()->name(),
            'name' => fake()->name(),
            'description' => fake()->name(),
            'notes' => fake()->name(),
            'price' => fake()->randomFloat(2, 0, 9999),
            'cost' => fake()->randomFloat(2, 0, 9999),
            'net_weight' => fake()->randomFloat(2, 0, 9999),
            'gross_weight' => fake()->randomFloat(2, 0, 9999),
            'width' => fake()->randomFloat(2, 0, 9999),
            'height' => fake()->randomFloat(2, 0, 9999),
            'length' => fake()->randomFloat(2, 0, 9999),
            'minimum_stock' => fake()->randomFloat(2, 0, 9999),
            'maximum_stock' => fake()->randomFloat(2, 0, 9999),
            'is_active' => fake()->boolean(),
            'measurement_unit_id' => fake()->numberBetween(0, 100),
            'product_category_id' => fake()->numberBetween(0, 100),
            'product_brand_id' => fake()->numberBetween(0, 100)
        ];
    }
}
