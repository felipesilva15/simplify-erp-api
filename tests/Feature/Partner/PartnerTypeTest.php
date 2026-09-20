<?php

namespace Tests\Feature\Partner;

use App\Modules\Partner\Models\PartnerType;
use Tests\TestCase;
use Illuminate\Http\Response;

class PartnerTypeTest extends TestCase
{
    protected string $endpoint = '/api/partner/partner-types';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'code',
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

    public function test_can_list_partner_types(): void
    {
        PartnerType::factory(3)->create();
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

    public function test_can_list_partner_types_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        PartnerType::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_partner_types_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        PartnerType::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_cannot_list_partner_types_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_partner_types_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    protected function getLookupResourceStructure(): array {
        return [
            'key',
            'label',
            'sublabel',
            'meta' => [
                'id',
                'name',
                'code'
            ]
        ];
    }

    public function test_lookup_returns_default_api_response_structure(): void {
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_lookup_partner_types(): void
    {
        PartnerType::factory(3)->create();
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getLookupResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_lookup_partner_types_with_text_filter(): void
    {
        $partnerType = PartnerType::factory()->createOne(['name' => 'Tipo Atacado']);
        PartnerType::factory()->createOne(['name' => 'Tipo Varejo']);
        $queryParams = [
            'q' => 'Atacado'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $partnerType->code)
            ->assertJsonPath('data.0.label', $partnerType->name)
            ->assertJsonPath('data.0.sublabel', "Cod.: {$partnerType->code}")
            ->assertJsonPath('data.0.meta.id', $partnerType->id);
    }

    public function test_can_lookup_partner_types_filtered_by_keys(): void
    {
        $partnerTypes = PartnerType::factory(3)->create();
        $queryParams = [
            'keys' => [
                $partnerTypes[0]->code,
                $partnerTypes[2]->code
            ]
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['key' => $partnerTypes[0]->code])
            ->assertJsonFragment(['key' => $partnerTypes[2]->code]);
    }

    public function test_can_lookup_partner_types_with_pagination(): void
    {
        PartnerType::factory(5)->create();
        $queryParams = [
            'per_page' => 2,
            'page' => 2
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.total', 5);
    }

    public function test_cannot_lookup_partner_types_without_authentication(): void
    {
        $response = $this->getJson("{$this->endpoint}/lookup");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_get_partner_type_by_id(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);
    }

    public function test_cannot_get_partner_type_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_partner_type_by_id_without_authentication(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_partner_type_by_id_without_permission(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_partner_type_by_id_for_edit(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_partner_type_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_partner_type_by_id_for_edit_without_authentication(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_partner_type_by_id_for_edit_without_permission(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_partner_type(): void
    {
        $model = PartnerType::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $model->name);

        $this->assertDatabaseHas('partner_types', [
            'name' => $model->name,
        ]);
    }

    public function test_cannot_create_partner_type_with_invalid_payload(): void
    {
        $model = PartnerType::factory()->makeOne();
        $data = $model->toArray();
        unset($data['name']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_create_partner_type_without_authentication(): void
    {
        $model = PartnerType::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_partner_type_without_permission(): void
    {
        $model = PartnerType::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_partner_type(): void
    {
        $model = PartnerType::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated PartnerType';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'Updated PartnerType');
    }

    public function test_cannot_update_partner_type_with_invalid_payload(): void
    {
        $model = PartnerType::factory()->createOne();

        $data = $model->toArray();
        unset($data['name']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_update_partner_type_with_invalid_id(): void
    {
        $model = PartnerType::factory()->createOne();

        $data = $model->toArray();
        $data['name'] = 'Updated PartnerType';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_partner_type_without_authentication(): void
    {
        $model = PartnerType::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_partner_type_without_permission(): void
    {
        $model = PartnerType::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_partner_type_by_id(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('partner_types', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_partner_type_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_partner_type_without_authentication(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_partner_type_without_permission(): void
    {
        $model = PartnerType::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
