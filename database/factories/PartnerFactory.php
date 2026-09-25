<?php

namespace Database\Factories;

use App\Modules\Partner\Enums\GenderEnum;
use App\Modules\Partner\Enums\MaritalStatusEnum;
use App\Modules\Partner\Enums\PersonTypeEnum;
use App\Modules\Partner\Enums\PixTypeEnum;
use App\Modules\Partner\Enums\TaxpayerTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Partner\Models\Partner>
 */
class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'partner_type_code' => fake()->unique()->lexify('???'),
            'name' => fake()->name(),
            'trade_name' => fake()->name(),
            'person_type' => PersonTypeEnum::Company,
            'taxpayer_type' => TaxpayerTypeEnum::NonTaxpayer,
            'document_number' => fake()->unique()->lexify('??????????????'),
            'identity_number' => fake()->unique()->lexify('?????????'),
            'identity_issuer' => fake()->unique()->lexify('???'),
            'partner_since' => fake()->date('Y-m-d'),
            'state_registration' => fake()->unique()->numerify('############'),
            'municipal_registration' => fake()->unique()->numerify('########'),
            'suframa_registration' => fake()->unique()->numerify('########'),
            'marital_status' => MaritalStatusEnum::Single,
            'cbo' => fake()->unique()->numerify('######'),
            'gender' => GenderEnum::Male,
            'birth_date' => fake()->date('Y-m-d'),
            'father_name' => fake()->name(),
            'father_document' => fake()->unique()->lexify('?????????'),
            'mother_name' => fake()->name(),
            'mother_document' => fake()->unique()->lexify('?????????'),
            'pix_type' => PixTypeEnum::Email,
            'pix_key' => fake()->email(),
            'notes' => fake()->text(160)
        ];
    }
}
