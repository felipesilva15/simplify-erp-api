<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Catalog\Exports\ProductCategoryExport;
use Maatwebsite\Excel\Facades\Excel;use Tests\TestCase;
use Illuminate\Http\Response;

class ProductCategoryTest extends TestCase
{
    protected string $endpoint = '/api/catalog/product-categories';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'parent_category_id',
            'applicability',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_product_categorys(): void
    {
        ProductCategory::factory(3)->create();
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_product_categorys_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        ProductCategory::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_product_categorys_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        ProductCategory::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_can_list_product_categorys_with_search(): void
    {
        $models = ProductCategory::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name
        ];

        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->first()->id);
    }

    public function test_cannot_list_product_categorys_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_product_categorys_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_product_category_by_id(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);
    }

    public function test_cannot_get_product_category_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_product_category_by_id_without_authentication(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_product_category_by_id_without_permission(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_product_category_by_id_for_edit(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_product_category_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_product_category_by_id_for_edit_without_authentication(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_product_category_by_id_for_edit_without_permission(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_product_category(): void
    {
        $model = ProductCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);

        $this->assertDatabaseHas('product_categories', [
            'name' => $model->name,
        ]);
    }

    public function test_cannot_create_product_category_with_invalid_payload(): void
    {
        $model = ProductCategory::factory()->makeOne();
        $data = $model->toArray();
        unset($data['name']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_create_product_category_without_authentication(): void
    {
        $model = ProductCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_product_category_without_permission(): void
    {
        $model = ProductCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_product_category(): void
    {
        $model = ProductCategory::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated ProductCategory';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'Updated ProductCategory');
    }

    public function test_cannot_update_product_category_with_invalid_payload(): void
    {
        $model = ProductCategory::factory()->createOne();

        $data = $model->toArray();
        unset($data['name']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_update_product_category_with_invalid_id(): void
    {
        $model = ProductCategory::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated ProductCategory';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_product_category_without_authentication(): void
    {
        $model = ProductCategory::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_product_category_without_permission(): void
    {
        $model = ProductCategory::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_product_category_by_id(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('product_categories', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_product_category_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_product_category_without_authentication(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_product_category_without_permission(): void
    {
        $model = ProductCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_product_categorys(): void
    {
        ProductCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('product_category.xlsx', function (ProductCategoryExport $export) {
            return $export->query()->pluck('id')->all() === [3, 2, 1];
        });
    }

    public function test_can_export_product_categorys_with_search(): void
    {
        $models = ProductCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?q=".urlencode((string) $models->first()->name),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('product_category.xlsx', function (ProductCategoryExport $export) use ($models) {
            return $export->query()->pluck('id')->all() === [$models->first()->id];
        });
    }

    public function test_cannot_export_product_categorys_without_authentication(): void
    {
        ProductCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_lookup_product_categorys(): void
    {
        ProductCategory::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'label',
                        'sublabel',
                        'meta'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_lookup_product_categorys_with_search(): void
    {
        $models = ProductCategory::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->id);
    }

    public function test_can_lookup_product_categorys_with_sort(): void
    {
        $models = ProductCategory::factory(3)->create();

        $queryParams = [
            'sorts' => '-id'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.key', $models->last()->id);
    }

    public function test_cannot_lookup_product_categorys_without_authentication(): void
    {
        ProductCategory::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
