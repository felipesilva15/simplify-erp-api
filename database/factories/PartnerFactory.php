<?php

namespace Database\Factories;

use App\Modules\ThirdParty\Enums\PersonTypeEnum;
use App\Modules\ThirdParty\Enums\PixTypeEnum;
use App\Modules\ThirdParty\Enums\TaxpayerTypeEnum;
use App\Modules\ThirdParty\Models\Partner;
use App\Modules\ThirdParty\Models\PartnerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'partner_type_code' => fn () => PartnerType::factory()->createOne()->code,
            'name' => fake()->name(),
            'trade_name' => fake()->name(),
            'person_type' => PersonTypeEnum::Company,
            'taxpayer_type' => TaxpayerTypeEnum::NonTaxpayer,
            'document_number' => fake()->unique()->lexify('??????????????'),
            'partner_since' => fake()->date('Y-m-d'),
            'state_registration' => fake()->unique()->numerify('############'),
            'municipal_registration' => fake()->unique()->numerify('########'),
            'suframa_registration' => fake()->unique()->numerify('########'),
            'pix_type' => PixTypeEnum::Email,
            'pix_key' => fake()->email(),
            'notes' => fake()->text(160),
        ];
    }
}
