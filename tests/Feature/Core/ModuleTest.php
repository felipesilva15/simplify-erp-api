<?php

namespace Tests\Feature\Core;

use App\Core\Models\Module;
use Tests\TestCase;
use Illuminate\Http\Response;

class ModuleTest extends TestCase
{
    protected string $endpoint = '/api/core/modules';

    protected function getResourceStructure(): array {
        return [
            'id',
            'name',
            'description',
            'is_active',
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

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_modules(): void
    {
        Module::factory(3)->create();
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

    public function test_cannot_list_modules_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_modules_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_module_by_id(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $module->name);
    }

    public function test_cannot_get_module_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_module_by_id_without_authentication(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_module_by_id_without_permission(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_module_by_id_for_edit(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $module->name);
    }

    public function test_cannot_get_module_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_module_by_id_for_edit_without_authentication(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_module_by_id_for_edit_without_permission(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$module->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_module(): void
    {
        $module = Module::factory()->makeOne();
        $data = $module->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', $module->name);

        $this->assertDatabaseHas('modules', [
            'name' => $module->name,
        ]);
    }

    public function test_cannot_create_module_without_authentication(): void
    {
        $module = Module::factory()->makeOne();
        $data = $module->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_module_without_permission(): void
    {
        $module = Module::factory()->makeOne();
        $data = $module->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_module(): void
    {
        $module = Module::factory()->createOne();
        
        $data = $module->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson("{$this->endpoint}/{$module->id}", $data,  $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.name', 'New name');
    }

    public function test_cannot_update_module_with_invalid_id(): void
    {
        $module = Module::factory()->createOne();
        
        $data = $module->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson("{$this->endpoint}/999999", $data,  $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_module_without_authentication(): void
    {
        $module = Module::factory()->createOne();
        $data = $module->toArray();

        $response = $this->putJson("{$this->endpoint}/{$module->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_module_without_permission(): void
    {
        $module = Module::factory()->createOne();
        $data = $module->toArray();

        $response = $this->putJson("{$this->endpoint}/{$module->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_module_by_id(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$module->id}", [],  $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('modules', [
            'id' => $module->id,
        ]);
    }

    public function test_cannot_delete_module_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [],  $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_module_without_authentication(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$module->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_module_without_permission(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$module->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
