<?php

namespace Tests\Feature\Security;

use App\Core\Models\Resource;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\Role;
use Tests\TestCase;
use Illuminate\Http\Response;

class RoleTest extends TestCase
{
    protected string $endpoint = '/api/security/roles';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'description',
            'permissions' => [
                '*' => [
                    'id',
                    'resource',
                    'action',
                    'name',
                    'description'
                ]
            ],
            'updated_at',
            'created_at',
            'deleted_at'
        ];
    }

    protected function getLookupResourceStructure(): array {
        return [
            'key',
            'label',
            'sublabel',
            'meta' => [
                'id',
                'name'
            ]
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_roles(): void
    {
        Role::factory(3)->create();
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

    public function test_can_list_roles_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        $roles = Role::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', $roles->last()->id);
    }

    public function test_can_list_roles_with_filter(): void
    {
        $roles = Role::factory(3)->create();
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => $roles[1]->id
                ]
            ]
        ];
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $roles[1]->id);
    }

    public function test_cannot_list_roles_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_roles_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_lookup_returns_default_api_response_structure(): void {
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_lookup_roles(): void
    {
        Role::factory(3)->create();
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

    public function test_can_lookup_roles_with_text_filter(): void
    {
        $role = Role::factory()->createOne(['name' => 'Departamento Financeiro']);
        Role::factory()->createOne(['name' => 'Departamento Comercial']);
        $queryParams = [
            'q' => 'Financeiro'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $role->id)
            ->assertJsonPath('data.0.label', $role->name)
            ->assertJsonPath('data.0.sublabel', "Cod.: {$role->id}")
            ->assertJsonPath('data.0.meta.id', $role->id)
            ->assertJsonPath('data.0.meta.name', $role->name);
    }

    public function test_can_lookup_roles_filtered_by_keys(): void
    {
        $roles = Role::factory(3)->create();
        $queryParams = [
            'keys' => [
                $roles[0]->id,
                $roles[2]->id
            ]
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['key' => $roles[0]->id])
            ->assertJsonFragment(['key' => $roles[2]->id]);
    }

    public function test_can_lookup_roles_with_pagination(): void
    {
        Role::factory(5)->create();
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

    public function test_cannot_lookup_roles_without_authentication(): void
    {
        $response = $this->getJson("{$this->endpoint}/lookup");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_get_role_by_id(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $role->name);
    }

    public function test_cannot_get_role_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_role_by_id_without_authentication(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_role_by_id_without_permission(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_role_by_id_for_edit(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $role->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_role_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_role_by_id_for_edit_without_authentication(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_role_by_id_for_edit_without_permission(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$role->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_role(): void
    {
        $role = Role::factory()->makeOne();
        $data = $role->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());
        
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $role->name);

        $this->assertDatabaseHas('roles', [
            'name' => $role->name
        ]);
    }

    public function test_cannot_create_role_with_invalid_payload(): void
    {
        $role = Role::factory()->makeOne();
        $data = $role->toArray();
        unset($data['name']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_create_role_without_authentication(): void
    {
        $role = Role::factory()->makeOne();
        $data = $role->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_role_without_permission(): void
    {
        $role = Role::factory()->makeOne();
        $data = $role->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->createOne();
        
        $data = $role->toArray();
        $data['description'] = 'New description';

        $response = $this->putJson("{$this->endpoint}/{$role->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.description', 'New description');
    }

    public function test_cannot_update_role_with_invalid_payload(): void
    {
        $role = Role::factory()->createOne();
        
        $data = $role->toArray();
        unset($data['name']);

        $response = $this->putJson("{$this->endpoint}/{$role->id}", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_cannot_update_role_with_invalid_id(): void
    {
        $role = Role::factory()->createOne();
        
        $data = $role->toArray();
        $data['description'] = 'New description';

        $response = $this->putJson("{$this->endpoint}/999999", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_role_without_authentication(): void
    {
        $role = Role::factory()->createOne();
        $data = $role->toArray();

        $response = $this->putJson("{$this->endpoint}/{$role->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_role_without_permission(): void
    {
        $role = Role::factory()->createOne();
        $data = $role->toArray();

        $response = $this->putJson("{$this->endpoint}/{$role->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_role_by_id(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$role->id}", [],  $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_cannot_delete_role_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_role_without_authentication(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$role->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_role_without_permission(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$role->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_define_role_permissions(): void
    {
        $role = Role::factory()->createOne();
        $permissions = Permission::factory(3)->withName()->for(Resource::factory()->forModule())->create();
        $data = [
            'ids' => $permissions->pluck('id')
        ];

        $response = $this->patchJson("{$this->endpoint}/{$role->id}/permissions", $data,  $this->getAdminAuthHeaders());
        
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonCount(3, 'data.permissions');

        $this->assertDatabaseHas('permission_role', [
            'role_id' =>  $response->json('data.id')
        ]);

        $this->assertDatabaseCount('permission_role', 3);
    }

    public function test_cannot_define_role_permissions_by_invalid_id(): void
    {
        $response = $this->patchJson("{$this->endpoint}/999999/permissions", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_define_role_permissions_without_authentication(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->patchJson("{$this->endpoint}/{$role->id}/permissions");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_define_role_permissions_without_permission(): void
    {
        $role = Role::factory()->createOne();

        $response = $this->patchJson("{$this->endpoint}/{$role->id}/permissions", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
