<?php

namespace Database\Factories;

use App\Modules\Catalog\Enums\MeasurementUnitCategoryEnum;
use App\Modules\Catalog\Models\MeasurementUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Catalog\Models\MeasurementUnit>
 */
class MeasurementUnitFactory extends Factory
{
    protected $model = MeasurementUnit::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => fake()->words(2, true),
            'symbol' => fake()->lexify('??'),
            'category' => fake()->randomElement(MeasurementUnitCategoryEnum::cases()),
            'base_unit_id' => null,
            'conversion_factor' => 1.0,
            'decimal_places' => fake()->numberBetween(0, 4),
            'external_code' => fake()->optional()->lexify('???'),
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
