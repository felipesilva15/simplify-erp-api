<?php

namespace Tests\Feature\Catalog;

use App\Core\Enums\ProductApplicabilityEnum;
use App\Core\Enums\ProductTypeEnum;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use App\Modules\Catalog\Exports\ServiceExport;
use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceTest extends TestCase
{
    protected string $endpoint = '/api/catalog/services';

    protected function getResourceStructure(): array
    {
        return [
            'id',
            'type',
            'item_type_code',
            'sku',
            'name',
            'description',
            'notes',
            'price',
            'cost',
            'is_active',
            'measurement_unit_id',
            'product_category_id',
            'deleted_at',
            'created_at',
            'updated_at',
        ];
    }

    private function validPayload(array $overrides = []): array
    {
        $category = ProductCategory::factory()->createOne([
            'applicability' => ProductApplicabilityEnum::Service,
        ]);

        return array_merge([
            'item_type_code' => ItemTypeCodeEnum::Service->value,
            'sku' => 'SERV-'.Str::upper(Str::random(8)),
            'name' => 'Consultoria em sistemas',
            'description' => 'Consultoria técnica especializada',
            'notes' => 'Cobrança por hora',
            'price' => '150.0000',
            'cost' => '80.0000',
            'is_active' => true,
            'measurement_unit_id' => MeasurementUnit::factory()->createOne()->id,
            'product_category_id' => $category->id,
        ], $overrides);
    }

    private function authHeadersForPermission(string $permission): array
    {
        $user = User::factory()->createOne();
        $role = Role::factory()->createOne();
        $permissionModel = Permission::create([
            'name' => $permission,
            'action' => Str::afterLast($permission, '.'),
            'label' => $permission,
            'description' => $permission,
        ]);

        $role->permissions()->attach($permissionModel);
        $user->roles()->attach($role);

        $token = JWTAuth::fromUser($user);
        $this->withCredentials()
            ->withUnencryptedCookie(config('jwt.cookie_name'), $token);

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_listing_returns_default_api_response_structure(): void
    {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_services(): void
    {
        Product::factory()->service()->count(3)->create();

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

    public function test_can_list_services_with_sort(): void
    {
        Product::factory()->service()->count(3)->create();

        $response = $this->getJson(
            url()->query($this->endpoint, ['sorts' => '-id']),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_services_with_filter(): void
    {
        $models = Product::factory()->service()->count(3)->create();

        $response = $this->getJson(
            url()->query($this->endpoint, ['filters' => ['id' => ['eq' => $models->get(1)->id]]]),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->get(1)->id);
    }

    public function test_cannot_list_services_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_services_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_services_facade_ignores_products(): void
    {
        Product::factory()->createOne();
        Product::factory()->service()->createOne();

        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', ProductTypeEnum::Service->value);
    }

    public function test_can_get_service_by_id(): void
    {
        $model = Product::factory()->service()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure(['data' => $this->getResourceStructure()])
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value)
            ->assertJsonPath('data.item_type_code', ItemTypeCodeEnum::Service->value);

        $data = $response->json('data');
        $this->assertArrayNotHasKey('gtin', $data);
        $this->assertArrayNotHasKey('net_weight', $data);
        $this->assertArrayNotHasKey('minimum_stock', $data);
        $this->assertArrayNotHasKey('product_brand_id', $data);
    }

    public function test_cannot_get_product_through_services_facade(): void
    {
        $product = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$product->id}", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_service_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_can_get_service_by_id_for_edit(): void
    {
        $model = Product::factory()->service()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_can_create_service(): void
    {
        $data = $this->validPayload();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure(['data' => $this->getResourceStructure()])
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value)
            ->assertJsonPath('data.sku', $data['sku']);

        $this->assertDatabaseHas('products', [
            'id' => $response->json('data.id'),
            'type' => ProductTypeEnum::Service->value,
            'item_type_code' => ItemTypeCodeEnum::Service->value,
        ]);
    }

    public function test_client_cannot_choose_service_type(): void
    {
        $data = $this->validPayload([
            'type' => ProductTypeEnum::Product->value,
            'gtin' => '7892236733554',
            'net_weight' => '123.000',
        ]);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value);

        $model = Product::findOrFail($response->json('data.id'));
        $this->assertNull($model->gtin);
        $this->assertNull($model->net_weight);
    }

    public function test_cannot_create_service_with_invalid_item_type_code(): void
    {
        $data = $this->validPayload(['item_type_code' => ItemTypeCodeEnum::MerchandiseForResale->value]);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('item_type_code');
    }

    public function test_cannot_create_service_with_product_category(): void
    {
        $category = ProductCategory::factory()->createOne([
            'applicability' => ProductApplicabilityEnum::Product,
        ]);
        $data = $this->validPayload(['product_category_id' => $category->id]);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cannot_create_service_without_authentication(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload());

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_update_service(): void
    {
        $model = Product::factory()->service()->createOne();
        $data = $this->validPayload([
            'sku' => $model->sku,
            'name' => 'Serviço atualizado',
        ]);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.name', 'Serviço atualizado')
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value);

        $this->assertDatabaseHas('products', [
            'id' => $model->id,
            'name' => 'Serviço atualizado',
            'type' => ProductTypeEnum::Service->value,
        ]);
    }

    public function test_cannot_change_service_type_on_update(): void
    {
        $model = Product::factory()->service()->createOne();
        $data = $this->validPayload([
            'sku' => $model->sku,
            'type' => ProductTypeEnum::Product->value,
        ]);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.type', ProductTypeEnum::Service->value);

        $this->assertDatabaseHas('products', [
            'id' => $model->id,
            'type' => ProductTypeEnum::Service->value,
        ]);
    }

    public function test_cannot_update_service_with_invalid_item_type_code(): void
    {
        $model = Product::factory()->service()->createOne();
        $data = $this->validPayload([
            'sku' => $model->sku,
            'item_type_code' => ItemTypeCodeEnum::MerchandiseForResale->value,
        ]);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('item_type_code');
    }

    public function test_can_delete_service_by_id(): void
    {
        $model = Product::factory()->service()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());

        $response->assertNoContent();
        $this->assertSoftDeleted('products', ['id' => $model->id]);
    }

    public function test_cannot_delete_product_through_services_facade(): void
    {
        $product = Product::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$product->id}", [], $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_can_export_services(): void
    {
        Product::factory()->service()->count(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('service.xlsx', function (ServiceExport $export) {
            return $export->query()->pluck('type')->unique()->all() === [ProductTypeEnum::Service->value];
        });
    }

    public function test_can_lookup_services(): void
    {
        Product::factory()->service()->count(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'label', 'sublabel', 'meta'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_lookup_services_with_search(): void
    {
        $models = Product::factory()->service()->count(3)->create();

        $response = $this->getJson(
            url()->query("{$this->endpoint}/lookup", ['q' => $models->first()->name]),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->id);
    }

    public function test_can_get_service_activity_logs(): void
    {
        $model = Product::factory()->service()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/activity-logs", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_services_permission_does_not_grant_products_access(): void
    {
        $headers = $this->authHeadersForPermission('services.viewAny');

        $this->getJson($this->endpoint, $headers)->assertStatus(Response::HTTP_OK);
        $this->getJson('/api/catalog/products', $headers)->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_products_permission_does_not_grant_services_access(): void
    {
        $headers = $this->authHeadersForPermission('products.viewAny');

        $this->getJson('/api/catalog/products', $headers)->assertStatus(Response::HTTP_OK);
        $this->getJson($this->endpoint, $headers)->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_accepts_all_service_item_type_codes(): void
    {
        foreach (ItemTypeCodeEnum::valuesFor(ProductTypeEnum::Service) as $code) {
            $data = $this->validPayload([
                'item_type_code' => $code,
                'sku' => 'SERV-'.Str::upper(Str::random(8)),
            ]);

            $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

            $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonPath('data.item_type_code', $code)
                ->assertJsonPath('data.type', ProductTypeEnum::Service->value);
        }
    }

    public function test_cannot_access_product_activity_logs_through_services_facade(): void
    {
        $product = Product::factory()->createOne();

        $response = $this->getJson(
            "{$this->endpoint}/{$product->id}/activity-logs",
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }
}
