<?php

namespace Database\Factories;

use App\Core\Enums\ProductApplicabilityEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Catalog\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'applicability' => ProductApplicabilityEnum::Product,
            'is_active' => true
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withParentCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_category_id' => $this->factoryForModel(\App\Modules\Catalog\Models\ProductCategory::class)->create()->id,
        ]);
    }
}
