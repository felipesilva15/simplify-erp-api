<?php

namespace Tests\Feature\Security;

use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use Tests\TestCase;
use Illuminate\Http\Response;

class UserTest extends TestCase
{
    protected string $endpoint = '/api/security/users';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'username',
            'phone_number',
            'is_admin',
            'roles' => [
                '*' => [
                    'id',
                    'name'
                ]
            ],
            'permissions',
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

    public function test_can_list_users(): void
    {
        User::factory(3)->create();
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

    public function test_cannot_list_users_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_users_without_user(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_user_by_id(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $user->name);
    }

    public function test_cannot_get_user_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_user_by_id_without_authentication(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_user_by_id_without_user(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_user_by_id_for_edit(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_user_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_user_by_id_for_edit_without_authentication(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_user_by_id_for_edit_without_user(): void
    {
        $user = User::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$user->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_user(): void
    {
        $user = User::factory()->makeOne();
        $data = $user->toArray();
        $data['password'] = User::factory()->getDefaultPassword();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $user->name);

        $this->assertDatabaseHas('users', [
            'resource' => $user->resource,
            'action' => $user->action,
        ]);
    }

    public function test_can_create_user_with_roles(): void
    {
        $roles = Role::factory(2)->create();

        $user = User::factory()->makeOne();
        $data = $user->toArray();

        $data['password'] = User::factory()->getDefaultPassword();
        $data['roles'] = $roles->select('id');

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonCount(2, 'data.roles');

        $this->assertDatabaseHas('users', [
            'username' => $user->username
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' =>  $response->json('data.id')
        ]);

        $this->assertDatabaseCount('role_user', 2);
    }

    public function test_cannot_create_user_without_authentication(): void
    {
        $user = User::factory()->makeOne();
        $data = $user->toArray();
        $data['password'] = User::factory()->getDefaultPassword();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_user_without_user(): void
    {
        $user = User::factory()->makeOne();
        $data = $user->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->createOne();
        
        $data = $user->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson("{$this->endpoint}/{$user->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'New name');
    }
    
    public function test_can_update_user_with_roles(): void
    {
        $user = User::factory()->has(Role::factory()->count(2))->createOne();
        $data = $user->toArray();

        $roles = Role::factory(3)->create();

        $data['roles'] = $roles->select('id');
        $data['name'] = 'New name';

    $response = $this->putJson("{$this->endpoint}/{$user->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'New name')
            ->assertJsonCount(3, 'data.roles');

        $this->assertDatabaseHas('users', [
            'username' => $user->username
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' =>  $response->json('data.id')
        ]);

        $this->assertDatabaseCount('role_user', 3);
    }

    public function test_cannot_update_user_with_invalid_id(): void
    {
        $user = User::factory()->createOne();
        
        $data = $user->toArray();
        $data['description'] = 'New description';

        $response = $this->putJson("{$this->endpoint}/999999", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_user_without_authentication(): void
    {
        $user = User::factory()->createOne();
        $data = $user->toArray();

        $response = $this->putJson("{$this->endpoint}/{$user->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_user_without_user(): void
    {
        $user = User::factory()->createOne();
        $data = $user->toArray();

        $response = $this->putJson("{$this->endpoint}/{$user->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_user_by_id(): void
    {
        $user = User::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$user->id}", [],  $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_cannot_delete_user_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_user_without_authentication(): void
    {
        $user = User::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$user->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_user_without_user(): void
    {
        $user = User::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$user->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
