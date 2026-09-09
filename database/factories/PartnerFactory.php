<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Partner\Models\Partner>
 */
class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'partner_type_code' => fake()->name(),
            'name' => fake()->name(),
            'trade_name' => fake()->name(),
            'person_type' => fake()->name(),
            'taxpayer_type' => fake()->name(),
            'document_number' => fake()->name(),
            'identity_number' => fake()->name(),
            'identity_issuer' => fake()->name(),
            'partner_since' => fake()->date('Y-m-d H:i:s'),
            'state_registration' => fake()->name(),
            'municipal_registration' => fake()->name(),
            'suframa_registration' => fake()->name(),
            'marital_status' => fake()->name(),
            'cbo' => fake()->name(),
            'gender' => fake()->name(),
            'birth_date' => fake()->date('Y-m-d H:i:s'),
            'father_name' => fake()->name(),
            'father_document' => fake()->name(),
            'mother_name' => fake()->name(),
            'mother_document' => fake()->name(),
            'pix_type' => fake()->name(),
            'pix_key' => fake()->name(),
            'notes' => fake()->name()
        ];
    }
}
