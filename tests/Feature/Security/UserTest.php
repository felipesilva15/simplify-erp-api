<?php

namespace Tests\Feature\Security;

use App\Core\Enums\SqlOrderDirectionEnum;
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

    protected function getLookupResourceStructure(): array {
        return [
            'key',
            'label',
            'sublabel',
            'meta' => [
                'id',
                'name',
                'email',
                'username',
                'phone_number'
            ]
        ];
    }

    public function test_lookup_returns_default_api_response_structure(): void {
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_lookup_users(): void
    {
        User::factory(3)->create();
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getLookupResourceStructure()
                ]
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_can_lookup_users_with_text_filter(): void
    {
        $user = User::factory()->createOne(['name' => 'Felipe Oliveira']);
        User::factory()->createOne(['name' => 'Maria Silva']);
        $queryParams = [
            'q' => 'Felipe'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $user->id)
            ->assertJsonPath('data.0.label', $user->name)
            ->assertJsonPath('data.0.sublabel', "Cod.: {$user->id} | Email: {$user->email}")
            ->assertJsonPath('data.0.meta.id', $user->id)
            ->assertJsonPath('data.0.meta.name', $user->name)
            ->assertJsonPath('data.0.meta.email', $user->email);
    }

    public function test_can_lookup_users_filtered_by_keys(): void
    {
        $users = User::factory(3)->create();
        $queryParams = [
            'keys' => [
                $users[0]->id,
                $users[2]->id
            ]
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['key' => $users[0]->id])
            ->assertJsonFragment(['key' => $users[2]->id]);
    }

    public function test_can_lookup_users_with_pagination(): void
    {
        User::factory(5)->create();
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
            ->assertJsonPath('meta.last_page', 4)
            ->assertJsonPath('meta.total', 7);
    }

    public function test_cannot_lookup_users_without_authentication(): void
    {
        $response = $this->getJson("{$this->endpoint}/lookup");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
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
            ]);
    }

    public function test_can_list_users_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        User::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 5);
    }

    public function test_can_list_users_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        User::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
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
            'username' => $user->username
        ]);
    }

    public function test_cannot_create_user_with_invalid_payload(): void
    {
        $user = User::factory()->makeOne();
        $data = $user->toArray();
        unset($data['username']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('username');
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

    public function test_cannot_update_user_with_invalid_payload(): void
    {
        $user = User::factory()->createOne();
        
        $data = $user->toArray();
        unset($data['username']);

        $response = $this->putJson("{$this->endpoint}/{$user->id}", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('username');
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
