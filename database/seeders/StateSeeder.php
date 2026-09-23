<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryId = DB::table('countries')
            ->where('iso_code', 'BR')
            ->value('id');

        if (!$countryId) {
            $this->command->error("País com ISO Code 'BR' não foi encontrado.");
            return;
        }

        $now = now();

        $estados = [
            ['ibge_code' => 11, 'uf' => 'RO', 'name' => 'Rondônia', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 12, 'uf' => 'AC', 'name' => 'Acre', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 13, 'uf' => 'AM', 'name' => 'Amazonas', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 14, 'uf' => 'RR', 'name' => 'Roraima', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 15, 'uf' => 'PA', 'name' => 'Pará', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 16, 'uf' => 'AP', 'name' => 'Amapá', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 17, 'uf' => 'TO', 'name' => 'Tocantins', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 21, 'uf' => 'MA', 'name' => 'Maranhão', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 22, 'uf' => 'PI', 'name' => 'Piauí', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 23, 'uf' => 'CE', 'name' => 'Ceará', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 24, 'uf' => 'RN', 'name' => 'Rio Grande do Norte', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 25, 'uf' => 'PB', 'name' => 'Paraíba', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 26, 'uf' => 'PE', 'name' => 'Pernambuco', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 27, 'uf' => 'AL', 'name' => 'Alagoas', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 28, 'uf' => 'SE', 'name' => 'Sergipe', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 29, 'uf' => 'BA', 'name' => 'Bahia', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 31, 'uf' => 'MG', 'name' => 'Minas Gerais', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 32, 'uf' => 'ES', 'name' => 'Espírito Santo', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 33, 'uf' => 'RJ', 'name' => 'Rio de Janeiro', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 35, 'uf' => 'SP', 'name' => 'São Paulo', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 41, 'uf' => 'PR', 'name' => 'Paraná', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 42, 'uf' => 'SC', 'name' => 'Santa Catarina', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 43, 'uf' => 'RS', 'name' => 'Rio Grande do Sul', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 50, 'uf' => 'MS', 'name' => 'Mato Grosso do Sul', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 51, 'uf' => 'MT', 'name' => 'Mato Grosso', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 52, 'uf' => 'GO', 'name' => 'Goiás', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
            ['ibge_code' => 53, 'uf' => 'DF', 'name' => 'Distrito Federal', 'country_id' => $countryId, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('states')->upsert(
            $estados,
            ['ibge_code'],
            ['name', 'uf', 'country_id', 'updated_at']
        );
    }
}
