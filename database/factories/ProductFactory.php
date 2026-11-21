<?php

namespace Database\Factories;

use App\Core\Enums\ProductApplicabilityEnum;
use App\Core\Enums\ProductTypeEnum;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductBrand;
use App\Modules\Catalog\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => ProductTypeEnum::Product,
            'item_type_code' => ItemTypeCodeEnum::MerchandiseForResale->value,
            'sku' => fake()->unique()->bothify('SKU-####'),
            'gtin' => fake()->numerify('#############'),
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
            'minimum_stock' => 1,
            'maximum_stock' => 10,
            'is_active' => fake()->boolean(),
            'measurement_unit_id' => MeasurementUnit::factory(),
            'product_category_id' => ProductCategory::factory(),
            'product_brand_id' => ProductBrand::factory(),
        ];
    }

    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ProductTypeEnum::Service,
            'item_type_code' => ItemTypeCodeEnum::Service->value,
            'gtin' => null,
            'net_weight' => null,
            'gross_weight' => null,
            'width' => null,
            'height' => null,
            'length' => null,
            'minimum_stock' => null,
            'maximum_stock' => null,
            'product_category_id' => ProductCategory::factory()->state([
                'applicability' => ProductApplicabilityEnum::Service,
            ]),
            'product_brand_id' => null,
        ]);
    }
}
