<?php

namespace Tests\Feature\Core;

use App\Core\Models\Resource;
use App\Modules\Security\Models\Permission;
use Tests\TestCase;
use Illuminate\Http\Response;

class ResourceTest extends TestCase
{
    protected string $endpoint = '/api/core/resources';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'slug',
            'description',
            'module' => [
                'id',
                'name',
                'slug'
            ],
            'permissions' => [
                '*' => [
                    'id',
                    'action',
                    'name',
                    'label',
                    'description'
                ]
            ],
            'updated_at',
            'created_at',
            'deleted_at'
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_resources(): void
    {
        Resource::factory(3)->forModule()->forModule()->create();
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

    public function test_can_list_resources_with_permissions(): void
    {
        Resource::factory(3)->forModule()
            ->forModule()
            ->has(Permission::factory()->forResource()->withName()->count(2))
            ->create();
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonCount(2, 'data.0.permissions');
    }

    public function test_can_list_resources_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        Resource::factory(3)->forModule()->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_resources_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        Resource::factory(3)->forModule()->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_cannot_list_resources_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_resources_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_resource_by_id(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $resource->name);
    }

    public function test_cannot_get_resource_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_resource_by_id_without_authentication(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_resource_by_id_without_permission(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_resource_by_id_for_edit(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $resource->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_resource_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_resource_by_id_for_edit_without_authentication(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_resource_by_id_for_edit_without_permission(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$resource->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_resource(): void
    {
        $resource = Resource::factory()->forModule()->makeOne();
        $data = $resource->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $resource->name);

        $this->assertDatabaseHas('resources', [
            'name' => $resource->name,
        ]);
    }

    public function test_cannot_create_resource_with_invalid_payload(): void
    {
        $resource = Resource::factory()->forModule()->makeOne();
        $data = $resource->toArray();
        unset($data['name']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_create_resource_without_authentication(): void
    {
        $resource = Resource::factory()->forModule()->makeOne();
        $data = $resource->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_resource_without_permission(): void
    {
        $resource = Resource::factory()->forModule()->makeOne();
        $data = $resource->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_resource(): void
    {
        $resource = Resource::factory()->forModule()->createOne();
        
        $data = $resource->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson("{$this->endpoint}/{$resource->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'New name');
    }

    public function test_cannot_update_resource_with_invalid_payload(): void
    {
        $resource = Resource::factory()->forModule()->createOne();
        
        $data = $resource->toArray();
        unset($data['name']);

        $response = $this->putJson("{$this->endpoint}/{$resource->id}", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_update_resource_with_invalid_id(): void
    {
        $resource = Resource::factory()->forModule()->createOne();
        
        $data = $resource->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson("{$this->endpoint}/999999", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_resource_without_authentication(): void
    {
        $resource = Resource::factory()->forModule()->createOne();
        $data = $resource->toArray();

        $response = $this->putJson("{$this->endpoint}/{$resource->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_resource_without_permission(): void
    {
        $resource = Resource::factory()->forModule()->createOne();
        $data = $resource->toArray();

        $response = $this->putJson("{$this->endpoint}/{$resource->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_resource_by_id(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$resource->id}", [],  $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('resources', [
            'id' => $resource->id,
        ]);
    }

    public function test_cannot_delete_resource_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_resource_without_authentication(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$resource->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_resource_without_permission(): void
    {
        $resource = Resource::factory()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$resource->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
