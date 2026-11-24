<?php

namespace Tests\Feature\Finance;

use App\Modules\Finance\Models\PaymentMethod;
use App\Modules\Finance\Exports\PaymentMethodExport;
use Maatwebsite\Excel\Facades\Excel;use Tests\TestCase;
use Illuminate\Http\Response;

class PaymentMethodTest extends TestCase
{
    protected string $endpoint = '/api/finance/payment-methods';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'condition',
            'applicable_type',
            'tax_type',
            'tax_aliquot',
            'tax_fixed_value',
            'payment_deadline',
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

    public function test_can_list_payment_methods(): void
    {
        PaymentMethod::factory(3)->create();
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

    public function test_can_list_payment_methods_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        PaymentMethod::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_payment_methods_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        PaymentMethod::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_can_list_payment_methods_with_search(): void
    {
        $models = PaymentMethod::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name
        ];

        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $models->first()->id);
    }

    public function test_cannot_list_payment_methods_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_payment_methods_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_payment_method_by_id(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);
    }

    public function test_cannot_get_payment_method_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_payment_method_by_id_without_authentication(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_payment_method_by_id_without_permission(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_payment_method_by_id_for_edit(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_payment_method_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_payment_method_by_id_for_edit_without_authentication(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_payment_method_by_id_for_edit_without_permission(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_payment_method(): void
    {
        $model = PaymentMethod::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);

        $this->assertDatabaseHas('payment_methods', [
            'name' => $model->name,
        ]);
    }

    public function test_cannot_create_payment_method_with_invalid_payload(): void
    {
        $model = PaymentMethod::factory()->makeOne();
        $data = $model->toArray();
        unset($data['name']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_create_payment_method_without_authentication(): void
    {
        $model = PaymentMethod::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_payment_method_without_permission(): void
    {
        $model = PaymentMethod::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_payment_method(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated PaymentMethod';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'Updated PaymentMethod');
    }

    public function test_cannot_update_payment_method_with_invalid_payload(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $data = $model->toArray();
        unset($data['name']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_update_payment_method_with_invalid_id(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated PaymentMethod';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_payment_method_without_authentication(): void
    {
        $model = PaymentMethod::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_payment_method_without_permission(): void
    {
        $model = PaymentMethod::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_payment_method_by_id(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('payment_methods', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_payment_method_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_payment_method_without_authentication(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_payment_method_without_permission(): void
    {
        $model = PaymentMethod::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_payment_methods(): void
    {
        PaymentMethod::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('payment_method.xlsx', function (PaymentMethodExport $export) {
            return $export->query()->pluck('id')->all() === [3, 2, 1];
        });
    }

    public function test_can_export_payment_methods_with_search(): void
    {
        $models = PaymentMethod::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?q=".urlencode((string) $models->first()->name),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('payment_method.xlsx', function (PaymentMethodExport $export) use ($models) {
            return $export->query()->pluck('id')->all() === [$models->first()->id];
        });
    }

    public function test_cannot_export_payment_methods_without_authentication(): void
    {
        PaymentMethod::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_lookup_payment_methods(): void
    {
        PaymentMethod::factory(3)->create();

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

    public function test_can_lookup_payment_methods_with_search(): void
    {
        $models = PaymentMethod::factory(3)->create();

        $queryParams = [
            'q' => $models->first()->name
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $models->first()->id);
    }

    public function test_can_lookup_payment_methods_with_sort(): void
    {
        $models = PaymentMethod::factory(3)->create();

        $queryParams = [
            'sorts' => '-id'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.key', $models->last()->id);
    }

    public function test_cannot_lookup_payment_methods_without_authentication(): void
    {
        PaymentMethod::factory(3)->create();

        $response = $this->getJson("{$this->endpoint}/lookup");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
