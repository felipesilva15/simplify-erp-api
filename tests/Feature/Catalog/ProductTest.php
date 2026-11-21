<?php

namespace Tests\Feature\Catalog;

use App\Core\Enums\ProductTypeEnum;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use App\Modules\Catalog\Exports\ProductExport;
use App\Modules\Catalog\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProductTest extends TestCase
{
    protected string $endpoint = '/api/catalog/products';

    protected function getResourceStructure(): array
    {
        return [
            'id',
            'type',
            'item_type_code',
            'sku',
            'gtin',
            'name',
            'description',
            'notes',
            'price',
            'cost',
            'net_weight',
            'gross_weight',
            'width',
            'height',
            'length',
            'minimum_stock',
            'maximum_stock',
            'is_active',
            'measurement_unit_id',
            'product_category_id',
            'product_brand_id',
            'deleted_at',
            'created_at',
            'updated_at',
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void
    {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_products(): void
    {
        Product::factory(3)->create();
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

    public function test_can_list_products_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id',
        ];

        Product::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_products_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2,
                ],
            ],
        ];

        Product::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_can_list_products_with_search(): void
    {
        $models = Product::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name,
        ];

        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->first()->id);
    }

    public function test_cannot_list_products_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_products_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_product_by_id(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.type', $model->type);
    }

    public function test_cannot_get_product_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_product_by_id_without_authentication(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_product_by_id_without_permission(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_product_by_id_for_edit(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.type', $model->type)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_product_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_product_by_id_for_edit_without_authentication(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_product_by_id_for_edit_without_permission(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_product(): void
    {
        $model = Product::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.type', $model->type);

        $this->assertDatabaseHas('products', [
            'type' => $model->type,
        ]);
    }

    public function test_cannot_create_product_with_invalid_payload(): void
    {
        $model = Product::factory()->makeOne();
        $data = $model->toArray();
        $data['item_type_code'] = '99';

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('item_type_code');
    }

    public function test_cannot_create_product_without_authentication(): void
    {
        $model = Product::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_product_without_permission(): void
    {
        $model = Product::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_product(): void
    {
        $model = Product::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Produto atualizado';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure(),
            ])
            ->assertJsonPath('data.name', 'Produto atualizado')
            ->assertJsonPath('data.type', ProductTypeEnum::Product->value);
    }

    public function test_cannot_update_product_with_invalid_payload(): void
    {
        $model = Product::factory()->createOne();

        $data = $model->toArray();
        $data['item_type_code'] = '09';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('item_type_code');
    }

    public function test_cannot_update_product_with_invalid_id(): void
    {
        $model = Product::factory()->createOne();

        $data = $model->toArray();
        $data['type'] = 'Updated Product';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_product_without_authentication(): void
    {
        $model = Product::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_product_without_permission(): void
    {
        $model = Product::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_product_by_id(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_product_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_product_without_authentication(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_product_without_permission(): void
    {
        $model = Product::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_products(): void
    {
        Product::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('product.xlsx', function (ProductExport $export) {
            return $export->query()->pluck('id')->all() === [3, 2, 1];
        });
    }

    public function test_can_export_products_with_search(): void
    {
        $models = Product::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?q=".urlencode((string) $models->first()->name),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('product.xlsx', function (ProductExport $export) use ($models) {
            return $export->query()->pluck('id')->all() === [$models->first()->id];
        });
    }

    public function test_cannot_export_products_without_authentication(): void
    {
        Product::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_lookup_products(): void
    {
        Product::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'label',
                        'sublabel',
                        'meta',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_lookup_products_with_search(): void
    {
        $models = Product::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name,
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->id);
    }

    public function test_can_lookup_products_with_sort(): void
    {
        $models = Product::factory(3)->create();

        $queryParams = [
            'sorts' => '-id',
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.key', $models->last()->id);
    }

    public function test_cannot_lookup_products_without_authentication(): void
    {
        Product::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_products_facade_ignores_services(): void
    {
        Product::factory()->createOne();
        $service = Product::factory()->service()->createOne();

        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonMissing(['id' => $service->id]);
    }

    public function test_cannot_access_service_through_products_facade(): void
    {
        $service = Product::factory()->service()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$service->id}", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_client_cannot_change_product_type(): void
    {
        $model = Product::factory()->createOne();
        $data = $model->toArray();
        $data['type'] = ProductTypeEnum::Service->value;

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.type', ProductTypeEnum::Product->value);

        $this->assertDatabaseHas('products', [
            'id' => $model->id,
            'type' => ProductTypeEnum::Product->value,
        ]);
    }

    public function test_accepts_all_product_item_type_codes(): void
    {
        foreach (ItemTypeCodeEnum::valuesFor(ProductTypeEnum::Product) as $code) {
            $data = Product::factory()->makeOne()->toArray();
            $data['item_type_code'] = $code;
            $data['sku'] = 'PROD-'.Str::upper(Str::random(8));
            $data['gtin'] = (string) fake()->unique()->numerify('#############');

            $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

            $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonPath('data.item_type_code', $code)
                ->assertJsonPath('data.type', ProductTypeEnum::Product->value);
        }
    }

    public function test_cannot_access_service_activity_logs_through_products_facade(): void
    {
        $service = Product::factory()->service()->createOne();

        $response = $this->getJson(
            "{$this->endpoint}/{$service->id}/activity-logs",
            $this->getAdminAuthHeaders()
        );

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }
}
