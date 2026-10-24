<?php

namespace Tests\Feature\Catalog;

use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Exports\MeasurementUnitExport;
use Maatwebsite\Excel\Facades\Excel;use Tests\TestCase;
use Illuminate\Http\Response;

class MeasurementUnitTest extends TestCase
{
    protected string $endpoint = '/api/catalog/measurement-units';

    protected function getResourceStructure(): array {
        return [
            'id',
            'code',
            'name',
            'symbol',
            'category',
            'base_unit_id',
            'conversion_factor',
            'decimal_places',
            'external_code',
            'is_system',
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

    public function test_can_list_measurement_units(): void
    {
        MeasurementUnit::factory(3)->create();
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

    public function test_can_list_measurement_units_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        MeasurementUnit::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_measurement_units_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        MeasurementUnit::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_can_list_measurement_units_with_search(): void
    {
        $models = MeasurementUnit::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->code
        ];

        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->first()->id);
    }

    public function test_cannot_list_measurement_units_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_measurement_units_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_measurement_unit_by_id(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code);
    }

    public function test_cannot_get_measurement_unit_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_measurement_unit_by_id_without_authentication(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_measurement_unit_by_id_without_permission(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_measurement_unit_by_id_for_edit(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_measurement_unit_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_measurement_unit_by_id_for_edit_without_authentication(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_measurement_unit_by_id_for_edit_without_permission(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_measurement_unit(): void
    {
        $model = MeasurementUnit::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', $model->code);

        $this->assertDatabaseHas('measurement_units', [
            'code' => $model->code,
        ]);
    }

    public function test_cannot_create_measurement_unit_with_invalid_payload(): void
    {
        $model = MeasurementUnit::factory()->makeOne();
        $data = $model->toArray();
        unset($data['code']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('code');
    }

    public function test_cannot_create_measurement_unit_without_authentication(): void
    {
        $model = MeasurementUnit::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_measurement_unit_without_permission(): void
    {
        $model = MeasurementUnit::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_measurement_unit(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $data = $model->toArray();
        $data['code'] = 'UPDATED';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.code', 'UPDATED');
    }

    public function test_cannot_update_measurement_unit_with_invalid_payload(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $data = $model->toArray();
        unset($data['code']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('code');
    }

    public function test_cannot_update_measurement_unit_with_invalid_id(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $data = $model->toArray();
        $data['code'] = 'UPDATED';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_measurement_unit_without_authentication(): void
    {
        $model = MeasurementUnit::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_measurement_unit_without_permission(): void
    {
        $model = MeasurementUnit::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_cannot_update_measurement_unit_with_itself_as_base(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $data = $model->toArray();
        $data['base_unit_id'] = $model->id;

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('base_unit_id');

        $model->refresh();
        $this->assertNull($model->base_unit_id);
    }

    public function test_can_delete_measurement_unit_by_id(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('measurement_units', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_system_measurement_unit(): void
    {
        $model = MeasurementUnit::factory()->createOne(['is_system' => true]);

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertNotSoftDeleted('measurement_units', ['id' => $model->id]);
    }

    public function test_cannot_delete_measurement_unit_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_measurement_unit_without_authentication(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_measurement_unit_without_permission(): void
    {
        $model = MeasurementUnit::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_measurement_units(): void
    {
        MeasurementUnit::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('measurement_unit.xlsx', function (MeasurementUnitExport $export) {
            return $export->query()->pluck('id')->all() === [3, 2, 1];
        });
    }

    public function test_can_export_measurement_units_with_search(): void
    {
        $models = MeasurementUnit::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?q=".urlencode((string) $models->first()->code),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('measurement_unit.xlsx', function (MeasurementUnitExport $export) use ($models) {
            return $export->query()->pluck('id')->all() === [$models->first()->id];
        });
    }

    public function test_cannot_export_measurement_units_without_authentication(): void
    {
        MeasurementUnit::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_lookup_measurement_units(): void
    {
        MeasurementUnit::factory(3)->create();

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

    public function test_can_lookup_measurement_units_with_search(): void
    {
        $models = MeasurementUnit::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->code
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->code);
    }

    public function test_can_lookup_measurement_units_with_sort(): void
    {
        $models = MeasurementUnit::factory(3)->create();

        $queryParams = [
            'sorts' => '-id'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.key', $models->last()->code);
    }

    public function test_cannot_lookup_measurement_units_without_authentication(): void
    {
        MeasurementUnit::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
