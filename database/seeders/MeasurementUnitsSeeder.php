<?php

namespace Database\Seeders;

use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\ThirdParty\Models\PartnerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Modules\Catalog\Enums\MeasurementUnitCategoryEnum;
use Illuminate\Database\Seeder;

class MeasurementUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measurementUnits = [
            [ 'code' => 'UN', 'name' => 'Unidade', 'symbol' => 'un', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'PC', 'name' => 'Peça', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'PAR', 'name' => 'Par', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CJ', 'name' => 'Conjunto', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CX', 'name' => 'Caixa', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'SC', 'name' => 'Saco', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'RL', 'name' => 'Rolo', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'VB', 'name' => 'Verba', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Count, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'KG', 'name' => 'Quilograma', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Mass, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'G', 'name' => 'Grama', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Mass, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MG', 'name' => 'Miligrama', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Mass, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'T', 'name' => 'Tonelada', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Mass, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'L', 'name' => 'Litro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Volume, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'ML', 'name' => 'Mililitro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Volume, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M3', 'name' => 'Metro cúbico', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Volume, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M', 'name' => 'Metro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MM', 'name' => 'Milimetro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CM', 'name' => 'Centímetro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'KM', 'name' => 'Quilômetro', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M2', 'name' => 'Metro quadrado', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'HA', 'name' => 'Hectare', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Length, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'H', 'name' => 'Hora', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Time, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MIN', 'name' => 'Minuto', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Time, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'DIA', 'name' => 'Dia', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Time, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MES', 'name' => 'Mês', 'symbol' => 'SUP', 'category' => MeasurementUnitCategoryEnum::Time, 'base_unit_id' => null, 'conversion_factor' => 1, 'decimal_places' => 0, 'external_code' => null, 'is_system' => true, 'is_active' => true ],
        ];

        foreach ($measurementUnits as $measurementUnit) {
            MeasurementUnit::firstOrCreate(
                ['code' => $measurementUnit['code']],
                $measurementUnit
            );
        }
    }
}
