<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = storage_path('data/cidades.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado em: {$csvPath}");
            return;
        }

        $statesMap = DB::table('states')->pluck('id', 'ibge_code');

        if ($statesMap->isEmpty()) {
            $this->command->error("Nenhum estado encontrado na tabela 'states'. Execute o EstadoSeeder primeiro.");
            return;
        }

        $file = fopen($csvPath, 'r');
        
        $header = fgetcsv($file, 1000, ',');

        $batch = [];
        $chunkSize = 500;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            $stateIbgeCode = trim($row[0] ?? '');
            $cityIbgeCode = trim($row[7] ?? '');
            $rawName = trim($row[8] ?? '');
            $name = mb_check_encoding($rawName, 'UTF-8') ? $rawName : mb_convert_encoding($rawName, 'UTF-8', 'ISO-8859-1');
        
            if (!$cityIbgeCode || !$stateIbgeCode || !$name) {
                continue;
            }

            $stateId = $statesMap->get($stateIbgeCode);

            if (!$stateId) {
                continue; 
            }

            $batch[] = [
                'ibge_code' => $cityIbgeCode,
                'name' => $name,
                'state_id' => $stateId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $chunkSize) {
                DB::table('cities')->upsert(
                    $batch,
                    ['ibge_code'],
                    ['name', 'state_id', 'updated_at']
                );
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('cities')->upsert(
                $batch,
                ['ibge_code'],
                ['name', 'state_id', 'updated_at']
            );
        }

        fclose($file);
    }
}
