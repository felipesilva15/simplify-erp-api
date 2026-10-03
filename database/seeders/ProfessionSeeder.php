<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = storage_path('data/cbo-ocupacao.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado em: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        
        $header = fgetcsv($file, 1000, ';'); 

        $batch = [];
        $chunkSize = 500;

        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            $cbo = trim($row[0] ?? '');
            $name = mb_convert_encoding(trim($row[1] ?? ''), 'UTF-8', 'ISO-8859-1');

            if (empty($cbo) || empty($name)) {
                continue;
            }

            $batch[] = [
                'cbo' => $cbo,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $chunkSize) {
                DB::table('professions')->upsert(
                    $batch,
                    ['cbo'],
                    ['name', 'updated_at']
                );
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('professions')->upsert(
                $batch,
                ['cbo'],
                ['name', 'updated_at']
            );
        }

        fclose($file);
    }
}
