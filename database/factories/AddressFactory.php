<?php

namespace Database\Factories;

use App\Modules\Geography\Enums\AddressTypeEnum;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Models\City;
use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Models\State;
use App\Modules\ThirdParty\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'addressable_type' => Relation::getMorphAlias(Partner::class),
            'addressable_id' => Partner::factory(),
            'type' => AddressTypeEnum::Billing,
            'is_primary' => false,
            'country_id' => Country::factory(),
            'state_id' => null,
            'city_id' => null,
            'district' => null,
            'street' => null,
            'number' => null,
            'state_name' => 'California',
            'city_name' => 'Sacramento',
            'address_line_1' => '2201 K Street',
            'address_line_2' => 'Suite 200',
            'postal_code' => '95814',
            'complement' => 'Sala 101',
            'notes' => null,
        ];
    }

    public function forAddressable(Model $addressable): static
    {
        return $this->state(fn (): array => [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);
    }

    public function national(): static
    {
        return $this->state(function (): array {
            $country = Country::firstOrCreate(
                ['iso_code' => 'BR'],
                ['name' => 'Brasil']
            );

            $state = State::factory()->createOne(['country_id' => $country->id]);
            $city = City::factory()->createOne(['state_id' => $state->id]);

            return [
                'country_id' => $country->id,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'district' => 'Centro',
                'street' => 'Rua das Flores',
                'number' => '42',
                'state_name' => null,
                'city_name' => null,
                'address_line_1' => null,
                'address_line_2' => null,
            ];
        });
    }
}
