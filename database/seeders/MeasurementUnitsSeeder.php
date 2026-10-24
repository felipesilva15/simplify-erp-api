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
            [ 'code' => 'UN',   'name' => 'Unidade',        'symbol' => 'un',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'C62',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'PC',   'name' => 'Peça',           'symbol' => 'pç',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'H87',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'PAR',  'name' => 'Par',            'symbol' => 'par',  'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'PR',     'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CJ',   'name' => 'Conjunto',       'symbol' => 'cj',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'SET',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CX',   'name' => 'Caixa',          'symbol' => 'cx',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'BX',     'is_system' => true, 'is_active' => true ],
            [ 'code' => 'SC',   'name' => 'Saco',           'symbol' => 'sc',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'SA',     'is_system' => true, 'is_active' => true ],
            [ 'code' => 'RL',   'name' => 'Rolo',           'symbol' => 'rl',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => 'RO',     'is_system' => true, 'is_active' => true ],
            [ 'code' => 'VB',   'name' => 'Verba',          'symbol' => 'vb',   'category' => MeasurementUnitCategoryEnum::Count,   'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 0, 'external_code' => null,     'is_system' => true, 'is_active' => true ],
            [ 'code' => 'KG',   'name' => 'Quilograma',     'symbol' => 'kg',   'category' => MeasurementUnitCategoryEnum::Mass,    'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 3, 'external_code' => 'KGM',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'G',    'name' => 'Grama',          'symbol' => 'g',    'category' => MeasurementUnitCategoryEnum::Mass,    'base_unit_code' => 'KG',   'conversion_factor' => 0.001,       'decimal_places' => 3, 'external_code' => 'GRM',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MG',   'name' => 'Miligrama',      'symbol' => 'mg',   'category' => MeasurementUnitCategoryEnum::Mass,    'base_unit_code' => 'KG',   'conversion_factor' => 0.000001,    'decimal_places' => 3, 'external_code' => 'MGM',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'T',    'name' => 'Tonelada',       'symbol' => 't',    'category' => MeasurementUnitCategoryEnum::Mass,    'base_unit_code' => 'KG',   'conversion_factor' => 1000,        'decimal_places' => 3, 'external_code' => 'TNE',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'L',    'name' => 'Litro',          'symbol' => 'L',    'category' => MeasurementUnitCategoryEnum::Volume,  'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 3, 'external_code' => 'LTR',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'ML',   'name' => 'Mililitro',      'symbol' => 'mL',   'category' => MeasurementUnitCategoryEnum::Volume,  'base_unit_code' => 'L',    'conversion_factor' => 0.001,       'decimal_places' => 3, 'external_code' => 'MLT',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M3',   'name' => 'Metro cúbico',   'symbol' => 'm³',   'category' => MeasurementUnitCategoryEnum::Volume,  'base_unit_code' => 'L',    'conversion_factor' => 1000,        'decimal_places' => 3, 'external_code' => 'MTQ',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M',    'name' => 'Metro',          'symbol' => 'm',    'category' => MeasurementUnitCategoryEnum::Length,  'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 3, 'external_code' => 'MTR',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MM',   'name' => 'Milimetro',      'symbol' => 'mm',   'category' => MeasurementUnitCategoryEnum::Length,  'base_unit_code' => 'M',    'conversion_factor' => 0.001,       'decimal_places' => 3, 'external_code' => 'MMT',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'CM',   'name' => 'Centímetro',     'symbol' => 'cm',   'category' => MeasurementUnitCategoryEnum::Length,  'base_unit_code' => 'M',    'conversion_factor' => 0.01,        'decimal_places' => 3, 'external_code' => 'CMT',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'KM',   'name' => 'Quilômetro',     'symbol' => 'km',   'category' => MeasurementUnitCategoryEnum::Length,  'base_unit_code' => 'M',    'conversion_factor' => 1000,        'decimal_places' => 3, 'external_code' => 'KMT',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'M2',   'name' => 'Metro quadrado', 'symbol' => 'm²',   'category' => MeasurementUnitCategoryEnum::Area,    'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 3, 'external_code' => 'MTK',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'HA',   'name' => 'Hectare',        'symbol' => 'ha',   'category' => MeasurementUnitCategoryEnum::Area,    'base_unit_code' => 'M2',   'conversion_factor' => 10000,       'decimal_places' => 3, 'external_code' => 'HAR',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'H',    'name' => 'Hora',           'symbol' => 'h',    'category' => MeasurementUnitCategoryEnum::Time,    'base_unit_code' => null,   'conversion_factor' => 1,           'decimal_places' => 2, 'external_code' => 'HUR',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MIN',  'name' => 'Minuto',         'symbol' => 'min',  'category' => MeasurementUnitCategoryEnum::Time,    'base_unit_code' => 'H',    'conversion_factor' => 0,           'decimal_places' => 2, 'external_code' => 'MIN',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'DIA',  'name' => 'Dia',            'symbol' => 'dia',  'category' => MeasurementUnitCategoryEnum::Time,    'base_unit_code' => 'H',    'conversion_factor' => 0,           'decimal_places' => 2, 'external_code' => 'DAY',    'is_system' => true, 'is_active' => true ],
            [ 'code' => 'MES',  'name' => 'Mês',            'symbol' => 'mes',  'category' => MeasurementUnitCategoryEnum::Time,    'base_unit_code' => 'H',    'conversion_factor' => 0,           'decimal_places' => 2, 'external_code' => 'MON',    'is_system' => true, 'is_active' => true ],
        ];

        foreach ($measurementUnits as $measurementUnit) {
            $data = $measurementUnit;
            unset($data['base_unit_code']);

            $entity = MeasurementUnit::firstOrCreate(
                ['code' => $measurementUnit['code']],
                $data
            );

            if (is_null($measurementUnit['base_unit_code'])) {
                continue;
            }

            $baseUnit = MeasurementUnit::where('code', $measurementUnit['base_unit_code'])->first();

            if (!$baseUnit) {
                continue;
            }

            $entity->update([
                'base_unit_id' => $baseUnit->id
            ]);
        }
    }
}
