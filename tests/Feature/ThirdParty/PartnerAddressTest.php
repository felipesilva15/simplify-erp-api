<?php

namespace Tests\Feature\ThirdParty;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Models\ActivityLog;
use App\Modules\Geography\Enums\AddressTypeEnum;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Models\City;
use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Models\State;
use App\Modules\ThirdParty\Enums\PersonTypeEnum;
use App\Modules\ThirdParty\Enums\TaxpayerTypeEnum;
use App\Modules\ThirdParty\Models\Partner;
use App\Modules\ThirdParty\Models\PartnerType;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PartnerAddressTest extends TestCase
{
    protected string $endpoint = '/api/third-party/partners';

    private PartnerType $partnerType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->partnerType = PartnerType::factory()->createOne();
    }

    private function partnerPayload(array $overrides = []): array
    {
        return array_merge([
            'partner_type_code' => $this->partnerType->code,
            'name' => 'Empresa Exemplo LTDA',
            'trade_name' => 'Empresa Exemplo',
            'person_type' => PersonTypeEnum::Company->value,
            'taxpayer_type' => TaxpayerTypeEnum::NonTaxpayer->value,
            'document_number' => (string) fake()->unique()->numerify('#############'),
        ], $overrides);
    }

    private function foreignCountry(): Country
    {
        return Country::factory()->createOne();
    }

    private function addressItem(array $overrides = []): array
    {
        return array_merge([
            'type' => AddressTypeEnum::Billing->value,
            'is_primary' => false,
            'country_id' => $this->foreignCountry()->id,
            'state_name' => 'California',
            'city_name' => 'Sacramento',
            'address_line_1' => '2201 K Street',
            'address_line_2' => 'Suite 200',
            'postal_code' => '95814',
        ], $overrides);
    }

    private function nationalAddressItem(array $overrides = []): array
    {
        $country = Country::firstOrCreate(
            ['iso_code' => 'BR'],
            ['name' => 'Brasil']
        );

        $state = State::factory()->createOne(['country_id' => $country->id]);
        $city = City::factory()->createOne(['state_id' => $state->id]);

        return array_merge([
            'type' => AddressTypeEnum::Tax->value,
            'is_primary' => false,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'district' => 'Centro',
            'street' => 'Rua das Flores',
            'number' => '42',
        ], $overrides);
    }

    private function unchangedAddressPayloadFor(Address $address): array
    {
        return array_filter([
            'id' => $address->id,
            'type' => $address->type->value,
            'is_primary' => (bool) $address->is_primary,
            'country_id' => $address->country_id,
            'state_id' => $address->state_id,
            'city_id' => $address->city_id,
            'district' => $address->district,
            'street' => $address->street,
            'number' => $address->number,
            'state_name' => $address->state_name,
            'city_name' => $address->city_name,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'postal_code' => $address->postal_code,
            'complement' => $address->complement,
            'notes' => $address->notes,
        ], fn ($value) => $value !== null);
    }

    public function test_can_create_partner_with_addresses_in_a_single_request(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->addressItem(['type' => AddressTypeEnum::Billing->value, 'is_primary' => true]),
                $this->addressItem(['type' => AddressTypeEnum::Delivery->value, 'city_name' => 'Los Angeles']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(2, 'data.addresses')
            ->assertJsonPath('data.addresses.0.type', AddressTypeEnum::Billing->value)
            ->assertJsonPath('data.addresses.0.is_primary', true)
            ->assertJsonPath('data.addresses.0.addressable_type', 'partner')
            ->assertJsonPath('data.addresses.1.type', AddressTypeEnum::Delivery->value)
            ->assertJsonPath('data.addresses.1.city_name', 'Los Angeles');

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => 'partner',
            'addressable_id' => $response->json('data.id'),
            'type' => AddressTypeEnum::Billing->value,
        ]);

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => 'partner',
            'addressable_id' => $response->json('data.id'),
            'type' => AddressTypeEnum::Delivery->value,
        ]);
    }

    public function test_create_returns_summary_of_addresses_changes(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [$this->addressItem()],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('meta.children.addresses.created', 1)
            ->assertJsonPath('meta.children.addresses.updated', 0)
            ->assertJsonPath('meta.children.addresses.deleted', 0);
    }

    public function test_create_without_addresses_key_does_not_add_meta(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload(), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(0, 'data.addresses');

        $this->assertNull($response->json('meta'));
    }

    public function test_update_creates_updates_and_deletes_addresses(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        [$keep, $change, $remove] = $partner->addresses()->orderBy('id')->get()->all();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $this->unchangedAddressPayloadFor($keep),
                ['id' => $change->id, ...$this->unchangedAddressPayloadFor($change), 'postal_code' => '90000'],
                $this->addressItem(['city_name' => 'San Diego']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.addresses.created', 1)
            ->assertJsonPath('meta.children.addresses.updated', 1)
            ->assertJsonPath('meta.children.addresses.deleted', 1)
            ->assertJsonCount(3, 'data.addresses');

        $this->assertDatabaseHas('addresses', ['id' => $change->id, 'postal_code' => '90000']);
        $this->assertSoftDeleted('addresses', ['id' => $remove->id]);
        $this->assertDatabaseHas('addresses', ['city_name' => 'San Diego']);
    }

    public function test_update_creates_address_when_id_is_zero(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        [$first] = $partner->addresses()->orderBy('id')->get()->all();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $this->unchangedAddressPayloadFor($first),
                ['id' => 0, ...$this->addressItem(['city_name' => 'Novo endereço com id zero'])],
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.addresses.created', 1)
            ->assertJsonPath('meta.children.addresses.updated', 0)
            ->assertJsonPath('meta.children.addresses.deleted', 2)
            ->assertJsonCount(2, 'data.addresses');

        $this->assertDatabaseHas('addresses', [
            'addressable_id' => $partner->id,
            'city_name' => 'Novo endereço com id zero',
        ]);
    }

    public function test_update_without_addresses_key_preserves_addresses(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        $response = $this->putJson(
            "{$this->endpoint}/{$partner->id}",
            $this->partnerPayload(['name' => 'Empresa Renomeada']),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.name', 'Empresa Renomeada')
            ->assertJsonCount(3, 'data.addresses');

        $this->assertSame(3, Address::where('addressable_id', $partner->id)->count());
        $this->assertNull($response->json('meta'));
    }

    public function test_update_with_empty_addresses_list_removes_all(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.addresses.deleted', 3)
            ->assertJsonCount(0, 'data.addresses');

        $this->assertSame(0, Address::where('addressable_id', $partner->id)->count());
        $this->assertSame(3, Address::onlyTrashed()->where('addressable_id', $partner->id)->count());
    }

    public function test_unchanged_address_is_not_written_again(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        $address = $partner->addresses()->orderBy('id')->first();
        $originalUpdatedAt = $address->updated_at;

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $this->unchangedAddressPayloadFor($address),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.addresses.updated', 0)
            ->assertJsonPath('meta.children.addresses.deleted', 2);

        $this->assertTrue(
            $originalUpdatedAt->equalTo($address->fresh()->updated_at),
            'Endereço sem alteração não deve ser gravado novamente.'
        );
    }

    public function test_removed_address_is_restored_when_sent_again(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        $address = $partner->addresses()->orderBy('id')->first();

        $address->delete();
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $this->unchangedAddressPayloadFor($address),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.addresses.created', 0)
            ->assertJsonPath('meta.children.addresses.deleted', 2);

        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'deleted_at' => null]);
    }

    public function test_cannot_assign_address_from_another_partner(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        $other = $this->createPartnerWithThreeAddresses();
        $foreign = $other->addresses()->first();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                ['id' => $foreign->id, ...$this->addressItem()],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.0.id');

        $this->assertDatabaseHas('addresses', [
            'id' => $foreign->id,
            'addressable_id' => $other->id,
        ]);
    }

    public function test_whole_sync_is_rolled_back_when_an_address_is_invalid(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'name' => 'Nome que deve ser desfeito',
            'addresses' => [
                $this->addressItem(),
                $this->nationalAddressItem(['address_line_1' => 'Campo proibido']),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.1.address_line_1');

        $partner->refresh();
        $this->assertNotSame('Nome que deve ser desfeito', $partner->name);
        $this->assertSame(3, Address::where('addressable_id', $partner->id)->count());
    }

    public function test_cannot_create_foreign_address_without_foreign_fields(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                [
                    'type' => AddressTypeEnum::Billing->value,
                    'country_id' => $this->foreignCountry()->id,
                ],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.0.state_name');
        $response->assertJsonValidationErrorFor('addresses.0.address_line_1');

        $this->assertSame(0, Address::count());
        $this->assertSame(0, Partner::count());
    }

    public function test_cannot_create_national_address_missing_required_fields(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->nationalAddressItem(['street' => null, 'number' => null]),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.0.street');
        $response->assertJsonValidationErrorFor('addresses.0.number');

        $this->assertSame(0, Address::count());
        $this->assertSame(0, Partner::count());
    }

    public function test_national_address_rejects_foreign_fields(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->nationalAddressItem(['state_name' => 'California']),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.0.state_name');
    }

    public function test_foreign_address_rejects_national_fields(): void
    {
        $country = Country::firstOrCreate(['iso_code' => 'BR'], ['name' => 'Brasil']);
        $state = State::factory()->createOne(['country_id' => $country->id]);

        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->addressItem(['state_id' => $state->id]),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.0.state_id');
    }

    public function test_two_primary_addresses_on_same_partner_are_rejected(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->addressItem(['is_primary' => true]),
                $this->addressItem(['is_primary' => true]),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['addresses.0.is_primary', 'addresses.1.is_primary']);
        $this->assertSame(0, Address::count());
    }

    public function test_single_primary_address_is_accepted(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->addressItem(['is_primary' => true]),
                $this->addressItem(['is_primary' => false]),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('addresses', [
            'addressable_id' => $response->json('data.id'),
            'is_primary' => true,
        ]);
    }

    public function test_primary_rule_also_sees_primary_persisted_on_existing_addresses(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        [$first, $second] = $partner->addresses()->orderBy('id')->get()->all();

        $first->update(['is_primary' => true]);

        $withoutPrimary = $this->unchangedAddressPayloadFor($first);
        unset($withoutPrimary['is_primary']);

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $withoutPrimary,
                ['id' => $second->id, ...$this->unchangedAddressPayloadFor($second), 'is_primary' => true],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('addresses.1.is_primary');
    }

    public function test_each_address_operation_is_logged(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();
        [$keep, $change, $remove] = $partner->addresses()->orderBy('id')->get()->all();

        $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'addresses' => [
                $this->unchangedAddressPayloadFor($keep),
                ['id' => $change->id, ...$this->unchangedAddressPayloadFor($change), 'postal_code' => '90000'],
                $this->addressItem(['city_name' => 'Criado agora']),
            ],
        ]), $this->getAdminAuthHeaders())->assertStatus(Response::HTTP_OK);

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Address::morphAlias())
                ->where('origin_id', (string) $change->id)
                ->where('action', ActivityActionEnum::Updated->value)
                ->count(),
            'Endereço alterado deve ter log de alteração.'
        );

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Address::morphAlias())
                ->where('origin_id', (string) $remove->id)
                ->where('action', ActivityActionEnum::Deleted->value)
                ->count(),
            'Endereço removido deve ter log de exclusão.'
        );

        $created = Address::where('city_name', 'Criado agora')->firstOrFail();
        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Address::morphAlias())
                ->where('origin_id', (string) $created->id)
                ->where('action', ActivityActionEnum::Created->value)
                ->count(),
            'Endereço criado deve ter log de criação.'
        );

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Address::morphAlias())
                ->where('origin_id', (string) $keep->id)
                ->where('action', ActivityActionEnum::Created->value)
                ->count(),
            'Endereço sem alteração não deve gerar novo log.'
        );
    }

    public function test_address_history_is_queryable_by_morph_alias(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        $this->assertSame('address', Address::morphAlias());
        $this->assertSame(
            'address',
            Relation::getMorphAlias(Address::class),
            'O model de item precisa estar no morph map para o histórico ser consultável.'
        );

        $response = $this->getJson(
            "/api/third-party/partners/{$partner->id}/activity-logs",
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_list_loads_addresses_without_extra_queries(): void
    {
        Partner::factory(3)->create(['partner_type_code' => $this->partnerType->code]);
        $this->createPartnerWithThreeAddresses();

        $queries = $this->recordAddressQueriesFor(
            fn () => $this->getJson($this->endpoint, $this->getAdminAuthHeaders())
        );

        $this->assertCount(1, $queries, 'A listagem deve buscar os endereços uma única vez.');
    }

    public function test_show_single_partner_loads_addresses_without_extra_queries(): void
    {
        $partner = $this->createPartnerWithThreeAddresses();

        $queries = $this->recordAddressQueriesFor(
            fn () => $this->getJson("{$this->endpoint}/{$partner->id}", $this->getAdminAuthHeaders())
        );

        $this->assertCount(1, $queries, 'O detalhe deve buscar o parceiro e os endereços juntos.');
    }

    private function recordAddressQueriesFor(callable $request): array
    {
        $recorded = [];

        DB::listen(function ($query) use (&$recorded): void {
            if (str_contains($query->sql, '"addresses"')) {
                $recorded[] = $query->sql;
            }
        });

        $request();

        return $recorded;
    }

    private function createPartnerWithThreeAddresses(): Partner
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'addresses' => [
                $this->addressItem(['city_name' => 'Contato A']),
                $this->addressItem(['city_name' => 'Contato B']),
                $this->addressItem(['city_name' => 'Contato C']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED);

        return Partner::findOrFail($response->json('data.id'));
    }
}
