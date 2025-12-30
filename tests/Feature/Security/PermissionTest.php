<?php

namespace Tests\Feature\Security;

use App\Modules\Security\Models\Permission;
use Tests\TestCase;
use Illuminate\Http\Response;

class PermissionTest extends TestCase
{
    protected string $endpoint = '/api/security/permissions';

    protected function getResourceStructure(): array {
        return [
            'id',
            'resource',
            'action',
            'name',
            'description',
            'module' => [
                'id',
                'name'
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

    public function test_can_list_permissions(): void
    {
        Permission::factory(3)->withName()->forModule()->create();
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

    public function test_cannot_list_permissions_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_permissions_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_permission_by_id(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $permission->name);
    }

    public function test_cannot_get_permission_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_permission_by_id_without_authentication(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_permission_by_id_without_permission(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_permission_by_id_for_edit(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $permission->name);
    }

    public function test_cannot_get_permission_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_permission_by_id_for_edit_without_authentication(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_permission_by_id_for_edit_without_permission(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$permission->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_permission(): void
    {
        $permission = Permission::factory()->forModule()->makeOne();
        $data = $permission->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.resource', $permission->resource)
            ->assertJsonPath('data.action', $permission->action)
            ->assertJsonPath('data.name', $permission->resource.'.'.$permission->action);

        $this->assertDatabaseHas('permissions', [
            'resource' => $permission->resource,
            'action' => $permission->action,
        ]);
    }

    public function test_cannot_create_permission_without_authentication(): void
    {
        $permission = Permission::factory()->makeOne();
        $data = $permission->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_permission_without_permission(): void
    {
        $permission = Permission::factory()->makeOne();
        $data = $permission->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();
        
        $data = $permission->toArray();
        $data['description'] = 'New description';

        $response = $this->putJson("{$this->endpoint}/{$permission->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.description', 'New description');
    }

    public function test_cannot_update_permission_with_invalid_id(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();
        
        $data = $permission->toArray();
        $data['description'] = 'New description';

        $response = $this->putJson("{$this->endpoint}/999999", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_permission_without_authentication(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();
        $data = $permission->toArray();

        $response = $this->putJson("{$this->endpoint}/{$permission->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_permission_without_permission(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();
        $data = $permission->toArray();

        $response = $this->putJson("{$this->endpoint}/{$permission->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_permission_by_id(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$permission->id}", [],  $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_cannot_delete_permission_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_permission_without_authentication(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$permission->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_permission_without_permission(): void
    {
        $permission = Permission::factory()->withName()->forModule()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$permission->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
