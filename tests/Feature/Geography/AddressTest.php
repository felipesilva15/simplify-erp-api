<?php

namespace Tests\Feature\Geography;

use App\Modules\Geography\Enums\AddressTypeEnum;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Models\City;
use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Models\State;
use App\Modules\ThirdParty\Models\Partner;
use Illuminate\Http\Response;
use Tests\TestCase;

class AddressTest extends TestCase
{
    protected string $endpoint = '/api/geography/addresses';

    protected function getResourceStructure(): array
    {
        return [
            'id',
            'addressable_type',
            'addressable_id',
            'type',
            'is_primary',
            'country_id',
            'country_iso_code',
            'state_id',
            'city_id',
            'district',
            'street',
            'number',
            'state_name',
            'city_name',
            'address_line_1',
            'address_line_2',
            'postal_code',
            'complement',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    private function brazil(): Country
    {
        return Country::firstOrCreate(
            ['iso_code' => 'BR'],
            ['name' => 'Brasil']
        );
    }

    private function foreignCountry(): Country
    {
        return Country::factory()->createOne();
    }

    /** @return array{country: Country, state: State, city: City} */
    private function nationalPlaces(): array
    {
        $country = $this->brazil();
        $state = State::factory()->createOne(['country_id' => $country->id]);
        $city = City::factory()->createOne(['state_id' => $state->id]);

        return compact('country', 'state', 'city');
    }

    private function nationalPayload(Partner $partner, array $overrides = []): array
    {
        $places = $this->nationalPlaces();

        return array_merge([
            'addressable_type' => 'partner',
            'addressable_id' => $partner->id,
            'type' => AddressTypeEnum::Billing->value,
            'is_primary' => false,
            'country_id' => $places['country']->id,
            'state_id' => $places['state']->id,
            'city_id' => $places['city']->id,
            'district' => 'Centro',
            'street' => 'Rua das Flores',
            'number' => '42',
        ], $overrides);
    }

    private function foreignPayload(Partner $partner, array $overrides = []): array
    {
        return array_merge([
            'addressable_type' => 'partner',
            'addressable_id' => $partner->id,
            'type' => AddressTypeEnum::Delivery->value,
            'is_primary' => false,
            'country_id' => $this->foreignCountry()->id,
            'state_name' => 'California',
            'city_name' => 'Sacramento',
            'address_line_1' => '2201 K Street',
            'address_line_2' => 'Suite 200',
            'postal_code' => '95814',
        ], $overrides);
    }

    private function createPartner(): Partner
    {
        return Partner::factory()->createOne();
    }

    private function updatePayload(Partner $partner, array $overrides = []): array
    {
        $payload = $this->foreignPayload($partner, $overrides);
        unset($payload['addressable_type'], $payload['addressable_id']);

        return $payload;
    }

    public function test_listing_returns_default_api_response_structure(): void
    {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_addresses(): void
    {
        Address::factory(3)->create();

        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getResourceStructure(),
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_addresses_with_sort(): void
    {
        Address::factory(3)->create();

        $response = $this->getJson(
            url()->query($this->endpoint, ['sorts' => '-id']),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_addresses_with_filter_by_addressable(): void
    {
        $partner = $this->createPartner();
        Address::factory()->forAddressable($partner)->createOne();
        Address::factory(2)->create();

        $response = $this->getJson(
            url()->query($this->endpoint, [
                'filters' => [
                    'addressable_id' => ['eq' => $partner->id],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.addressable_id', $partner->id)
            ->assertJsonPath('data.0.addressable_type', 'partner');
    }

    public function test_cannot_list_addresses_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_addresses_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_address_by_id(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$address->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.id', $address->id);
    }

    public function test_cannot_get_address_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_address_by_id_without_authentication(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$address->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_address_by_id_without_permission(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$address->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_address_by_id_for_edit(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$address->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.id', $address->id)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_address_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_can_create_foreign_address(): void
    {
        $partner = $this->createPartner();

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.addressable_type', 'partner')
            ->assertJsonPath('data.addressable_id', $partner->id)
            ->assertJsonPath('data.type', AddressTypeEnum::Delivery->value)
            ->assertJsonPath('data.state_name', 'California');

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => 'partner',
            'addressable_id' => $partner->id,
            'state_name' => 'California',
        ]);
    }

    public function test_can_create_national_address(): void
    {
        $partner = $this->createPartner();
        $places = $this->nationalPlaces();

        $response = $this->postJson($this->endpoint, [
            'addressable_type' => 'partner',
            'addressable_id' => $partner->id,
            'type' => AddressTypeEnum::Billing->value,
            'is_primary' => false,
            'country_id' => $places['country']->id,
            'state_id' => $places['state']->id,
            'city_id' => $places['city']->id,
            'district' => 'Centro',
            'street' => 'Rua das Flores',
            'number' => '42',
        ], $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.country_iso_code', 'BR')
            ->assertJsonPath('data.state_id', $places['state']->id)
            ->assertJsonPath('data.city_id', $places['city']->id)
            ->assertJsonPath('data.district', 'Centro')
            ->assertJsonPath('data.street', 'Rua das Flores')
            ->assertJsonPath('data.number', '42');

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => 'partner',
            'addressable_id' => $partner->id,
            'country_id' => $places['country']->id,
            'city_id' => $places['city']->id,
        ]);
    }

    public function test_cannot_create_address_without_addressable(): void
    {
        $payload = $this->foreignPayload($this->createPartner());
        unset($payload['addressable_type'], $payload['addressable_id']);

        $response = $this->postJson($this->endpoint, $payload, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addressable_type');
        $response->assertJsonValidationErrorFor('addressable_id');
    }

    public function test_cannot_create_address_with_invalid_addressable_alias(): void
    {
        $partner = $this->createPartner();

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner, ['addressable_type' => 'inexistente']),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addressable_type');
    }

    public function test_cannot_create_address_without_valid_country(): void
    {
        $partner = $this->createPartner();

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner, ['country_id' => 999999]),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('country_id');
    }

    public function test_cannot_create_national_address_missing_required_fields(): void
    {
        $partner = $this->createPartner();
        $payload = $this->nationalPayload($partner);
        unset($payload['district'], $payload['street'], $payload['number']);

        $response = $this->postJson($this->endpoint, $payload, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('district');
        $response->assertJsonValidationErrorFor('street');
        $response->assertJsonValidationErrorFor('number');

        $this->assertSame(0, Address::count());
    }

    public function test_cannot_create_national_address_with_foreign_fields(): void
    {
        $partner = $this->createPartner();
        $places = $this->nationalPlaces();

        $response = $this->postJson(
            $this->endpoint,
            $this->nationalPayload($partner, ['state_name' => 'California']),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('state_name');
        $this->assertNotNull($places['country']);
    }

    public function test_cannot_create_foreign_address_with_national_fields(): void
    {
        $partner = $this->createPartner();
        $places = $this->nationalPlaces();

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner, ['state_id' => $places['state']->id]),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('state_id');
    }

    public function test_cannot_create_second_primary_address_for_same_owner(): void
    {
        $partner = $this->createPartner();
        $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner, ['is_primary' => true]),
            $this->getAdminAuthHeaders()
        )->assertStatus(Response::HTTP_CREATED);

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner, ['is_primary' => true]),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('is_primary');

        $this->assertSame(1, Address::where('addressable_id', $partner->id)->where('is_primary', true)->count());
    }

    public function test_can_create_primary_for_each_owner(): void
    {
        $first = $this->createPartner();
        $second = $this->createPartner();

        $this->postJson(
            $this->endpoint,
            $this->foreignPayload($first, ['is_primary' => true]),
            $this->getAdminAuthHeaders()
        )->assertStatus(Response::HTTP_CREATED);

        $this->postJson(
            $this->endpoint,
            $this->foreignPayload($second, ['is_primary' => true]),
            $this->getAdminAuthHeaders()
        )->assertStatus(Response::HTTP_CREATED);

        $this->assertSame(2, Address::where('is_primary', true)->count());
    }

    public function test_cannot_create_address_without_authentication(): void
    {
        $partner = $this->createPartner();

        $response = $this->postJson($this->endpoint, $this->foreignPayload($partner));
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_address_without_permission(): void
    {
        $partner = $this->createPartner();

        $response = $this->postJson(
            $this->endpoint,
            $this->foreignPayload($partner),
            $this->getCommomUserAuthHeaders()
        );
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_address(): void
    {
        $address = Address::factory()->createOne();

        $data = $this->updatePayload(
            Partner::findOrFail($address->addressable_id),
            ['postal_code' => '90000']
        );

        $response = $this->putJson("{$this->endpoint}/{$address->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.postal_code', '90000')
            ->assertJsonPath('data.addressable_type', 'partner')
            ->assertJsonPath('data.addressable_id', $address->addressable_id);
    }

    public function test_cannot_change_addressable_on_update(): void
    {
        $address = Address::factory()->createOne();
        $other = $this->createPartner();

        $data = $this->foreignPayload($other, ['addressable_id' => $other->id]);

        $response = $this->putJson(
            "{$this->endpoint}/{$address->id}",
            $data,
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addressable_id');
    }

    public function test_cannot_update_address_with_invalid_payload(): void
    {
        $address = Address::factory()->createOne();
        $partner = Partner::findOrFail($address->addressable_id);

        $data = $this->updatePayload($partner);
        unset($data['country_id']);

        $response = $this->putJson("{$this->endpoint}/{$address->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('country_id');
        $this->assertSame($address->fresh()->country_id, $address->country_id);
    }

    public function test_cannot_update_address_with_invalid_id(): void
    {
        $response = $this->putJson(
            "{$this->endpoint}/999999",
            $this->updatePayload($this->createPartner()),
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_address_without_authentication(): void
    {
        $address = Address::factory()->createOne();
        $partner = Partner::findOrFail($address->addressable_id);

        $response = $this->putJson("{$this->endpoint}/{$address->id}", $this->updatePayload($partner));
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_address_without_permission(): void
    {
        $address = Address::factory()->createOne();
        $partner = Partner::findOrFail($address->addressable_id);

        $response = $this->putJson(
            "{$this->endpoint}/{$address->id}",
            $this->updatePayload($partner),
            $this->getCommomUserAuthHeaders()
        );
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_address_by_id(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$address->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_cannot_delete_address_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_address_without_authentication(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$address->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_address_without_permission(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$address->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_list_address_activity_logs(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson(
            "{$this->endpoint}/{$address->id}/activity-logs",
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_cannot_list_address_activity_logs_without_permission(): void
    {
        $address = Address::factory()->createOne();

        $response = $this->getJson(
            "{$this->endpoint}/{$address->id}/activity-logs",
            $this->getCommomUserAuthHeaders()
        );
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
