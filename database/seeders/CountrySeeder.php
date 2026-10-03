<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $countries = [
            ['iso_code' => 'BR', 'name' => 'Brasil', 'created_at' => $now, 'updated_at' => $now]
        ];

        DB::table('countries')->upsert(
            $countries,
            ['iso_code'],
            ['name', 'updated_at']
        );
    }
}
