<?php

namespace Tests\Feature\Finance;

use App\Modules\Finance\Models\FinanceCategory;
use App\Modules\Finance\Exports\FinanceCategoryExport;
use Maatwebsite\Excel\Facades\Excel;use Tests\TestCase;
use Illuminate\Http\Response;

class FinanceCategoryTest extends TestCase
{
    protected string $endpoint = '/api/finance/finance-categories';

    protected function getResourceStructure(): array {
        return [
            'id',
            'parent_id',
            'code',
            'name',
            'applicable_type',
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

    public function test_can_list_finance_categorys(): void
    {
        FinanceCategory::factory(3)->create();
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

    public function test_can_list_finance_categorys_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        FinanceCategory::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_finance_categorys_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        FinanceCategory::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_can_list_finance_categorys_with_search(): void
    {
        $models = FinanceCategory::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->code
        ];

        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->first()->id);
    }

    public function test_cannot_list_finance_categorys_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_finance_categorys_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_finance_category_by_id(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code);
    }

    public function test_cannot_get_finance_category_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_finance_category_by_id_without_authentication(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_finance_category_by_id_without_permission(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_finance_category_by_id_for_edit(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_finance_category_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_finance_category_by_id_for_edit_without_authentication(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_finance_category_by_id_for_edit_without_permission(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_finance_category(): void
    {
        $model = FinanceCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code);

        $this->assertDatabaseHas('finance_categories', [
            'code' => $model->code,
        ]);
    }

    public function test_cannot_create_finance_category_with_invalid_payload(): void
    {
        $model = FinanceCategory::factory()->makeOne();
        $data = $model->toArray();
        unset($data['code']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('code');
    }

    public function test_cannot_create_finance_category_without_authentication(): void
    {
        $model = FinanceCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_finance_category_without_permission(): void
    {
        $model = FinanceCategory::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_finance_category(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $data = $model->toArray();
        $data['code'] = 'Updated FinanceCategory';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', 'Updated FinanceCategory');
    }

    public function test_cannot_update_finance_category_with_invalid_payload(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $data = $model->toArray();
        unset($data['code']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('code');
    }

    public function test_cannot_update_finance_category_with_invalid_id(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $data = $model->toArray();
        $data['code'] = 'Updated FinanceCategory';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_finance_category_without_authentication(): void
    {
        $model = FinanceCategory::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_finance_category_without_permission(): void
    {
        $model = FinanceCategory::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_finance_category_by_id(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('finance_categories', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_finance_category_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_finance_category_without_authentication(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_finance_category_without_permission(): void
    {
        $model = FinanceCategory::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_finance_categorys(): void
    {
        FinanceCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('finance_category.xlsx', function (FinanceCategoryExport $export) {
            return $export->query()->pluck('id')->all() === [3, 2, 1];
        });
    }

    public function test_can_export_finance_categorys_with_search(): void
    {
        $models = FinanceCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?q=".urlencode((string) $models->first()->code),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('finance_category.xlsx', function (FinanceCategoryExport $export) use ($models) {
            return $export->query()->pluck('id')->all() === [$models->first()->id];
        });
    }

    public function test_cannot_export_finance_categorys_without_authentication(): void
    {
        FinanceCategory::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_lookup_finance_categorys(): void
    {
        FinanceCategory::factory(3)->create();

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

    public function test_can_lookup_finance_categorys_with_search(): void
    {
        $models = FinanceCategory::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->code
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->code);
    }

    public function test_can_lookup_finance_categorys_with_filters(): void
    {
        $models = FinanceCategory::factory(2)->create();

        $queryParams = [
            'filters' => [
                'code' => ['eq' => $models->last()->code],
            ],
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->last()->code);
    }

    public function test_can_lookup_finance_categorys_with_sort(): void
    {
        $models = FinanceCategory::factory(3)->create();

        $queryParams = [
            'sorts' => '-id'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.key', $models->last()->code);
    }

    public function test_cannot_lookup_finance_categorys_without_authentication(): void
    {
        FinanceCategory::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
