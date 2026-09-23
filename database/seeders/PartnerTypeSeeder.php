<?php

namespace Database\Seeders;

use App\Modules\Partner\Models\PartnerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartnerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partnerTypes = [
            [
                'code' => 'SUP',
                'name' => 'Fornecedor'
            ],
            [
                'code' => 'CTM',
                'name' => 'Cliente'
            ],
            [
                'code' => 'SCT',
                'name' => 'Sub-contratado'
            ]
        ];

        foreach ($partnerTypes as $partnerType) {
            PartnerType::firstOrCreate(
                ['code' => $partnerType['code']],
                $partnerType
            );
        }
    }
}
