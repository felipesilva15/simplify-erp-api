<?php

namespace Tests\Feature\Core;

use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductBrand;
use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Construction\Models\ProjectType;
use App\Modules\Finance\Models\PaymentMethod;
use App\Modules\Geography\Models\City;
use App\Modules\Geography\Models\Country;
use App\Modules\Geography\Models\State;
use App\Modules\HR\Models\Profession;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use App\Modules\ThirdParty\Models\Partner;
use App\Modules\ThirdParty\Models\PartnerType;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LookupFiltersTest extends TestCase
{
    private function assertLookupFilteredBy(
        string $endpoint,
        callable $factory,
        string $field,
        string $keyField = 'id',
    ): void {
        /** @var Collection $models */
        $models = $factory();
        $target = $models->last();

        $response = $this->getJson(
            url()->query($endpoint, [
                'filters' => [
                    $field => ['eq' => $target->{$field}],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $target->{$keyField});
    }

    public function test_can_filter_users_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/security/users/lookup',
            fn () => User::factory()->count(2)->create(),
            'email',
        );
    }

    public function test_can_filter_roles_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/security/roles/lookup',
            fn () => Role::factory()->count(2)->create(),
            'id',
        );
    }

    public function test_can_filter_countries_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/geography/countries/lookup',
            fn () => Country::factory()->count(2)->create(),
            'iso_code',
        );
    }

    public function test_can_filter_states_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/geography/states/lookup',
            fn () => State::factory()->count(2)->forCountry()->create(),
            'uf',
        );
    }

    public function test_can_filter_cities_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/geography/cities/lookup',
            fn () => City::factory()->count(2)->for(State::factory()->forCountry())->create(),
            'ibge_code',
        );
    }

    public function test_can_filter_cities_lookup_by_state(): void
    {
        $state = State::factory()->forCountry()->create();
        $otherState = State::factory()->forCountry()->create();

        City::factory()->count(2)->create(['state_id' => $state->id]);
        City::factory()->create(['state_id' => $otherState->id]);

        $response = $this->getJson(
            url()->query('/api/geography/cities/lookup', [
                'filters' => [
                    'state_id' => ['eq' => $state->id],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_partner_types_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/third-party/partner-types/lookup',
            fn () => PartnerType::factory()->count(2)->create(),
            'code',
            'code',
        );
    }

    public function test_can_filter_partners_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/third-party/partners/lookup',
            fn () => Partner::factory()->count(2)->create(),
            'document_number',
        );
    }

    public function test_can_filter_professions_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/hr/professions/lookup',
            fn () => Profession::factory()->count(2)->create(),
            'cbo',
        );
    }

    public function test_can_filter_product_categories_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/catalog/product-categories/lookup',
            fn () => ProductCategory::factory()->count(2)->create(),
            'id',
        );
    }

    public function test_can_filter_product_brands_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/catalog/product-brands/lookup',
            fn () => ProductBrand::factory()->count(2)->create(),
            'id',
        );
    }

    public function test_can_filter_measurement_units_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/catalog/measurement-units/lookup',
            fn () => MeasurementUnit::factory()->count(2)->create(),
            'code',
        );
    }

    public function test_can_filter_products_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/catalog/products/lookup',
            fn () => Product::factory()->count(2)->create(),
            'sku',
        );
    }

    public function test_can_filter_services_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/catalog/services/lookup',
            fn () => Product::factory()->service()->count(2)->create(),
            'sku',
        );
    }

    public function test_can_filter_project_types_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/construction/project-types/lookup',
            fn () => ProjectType::factory()->count(2)->create(),
            'id',
        );
    }

    public function test_can_filter_payment_methods_lookup(): void
    {
        $this->assertLookupFilteredBy(
            '/api/finance/payment-methods/lookup',
            fn () => PaymentMethod::factory()->count(2)->create(),
            'id',
        );
    }

    public function test_products_lookup_accepts_gtin_filter(): void
    {
        $products = Product::factory()->count(2)->create();
        $target = $products->last();

        $response = $this->getJson(
            url()->query('/api/catalog/products/lookup', [
                'filters' => [
                    'gtin' => ['eq' => $target->gtin],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $target->id);
    }

    public function test_services_lookup_ignores_gtin_filter(): void
    {
        $products = Product::factory()->service()->count(2)->create();

        $response = $this->getJson(
            url()->query('/api/catalog/services/lookup', [
                'filters' => [
                    'gtin' => ['eq' => $products->last()->gtin],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_lookup_ignores_columns_outside_the_curated_filter_list(): void
    {
        Product::factory()->count(2)->create();

        $response = $this->getJson(
            url()->query('/api/catalog/products/lookup', [
                'filters' => [
                    'type' => ['eq' => 'SERVICE'],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_lookup_combines_filters_with_search_and_keys(): void
    {
        $users = User::factory()->count(3)->create();
        $target = $users->last();

        $response = $this->getJson(
            url()->query('/api/security/users/lookup', [
                'q' => $target->name,
                'keys' => [$target->id],
                'filters' => [
                    'id' => ['eq' => $target->id],
                ],
            ]),
            $this->getAdminAuthHeaders()
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $target->id);
    }
}
