<?php

namespace Tests\Feature\Core;

use App\Core\Models\Module;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ModuleTest extends TestCase
{
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
        $response = $this->getJson('/api/core/modules', $this->getAuthHeaders());

        $response->assertStatus(200);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_modules(): void
    {
        Module::factory(3)->create();
        $response = $this->getJson('/api/core/modules', $this->getAuthHeaders());

        $response->assertStatus(200)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_module_by_id(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson('/api/core/modules/'.$module->id, $this->getAuthHeaders());

        $response->assertStatus(200)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonFragment(['name' => $module->name]);
    }

    public function test_can_get_module_by_id_for_edit(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->getJson('/api/core/modules/'.$module->id.'/edit', $this->getAuthHeaders());

        $response->assertStatus(200)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonFragment(['name' => $module->name]);
    }

    public function test_cannot_get_module_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson('/api/core/modules/999999/edit', $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertNotFound()
                ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_get_module_by_invalid_id(): void
    {
        $response = $this->getJson('/api/core/modules/999999', $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertNotFound()
                ->assertJsonFragment(['success' => false]);
    }

    public function test_can_create_module(): void
    {
        $module = Module::factory()->makeOne();
        $data = $module->toArray();

        $response = $this->postJson('/api/core/modules/', $data, $this->getAuthHeaders());

        $response->assertStatus(201)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonFragment(['name' => $module->name]);
    }

    public function test_can_update_module(): void
    {
        $module = Module::factory()->createOne();
        
        $data = $module->toArray();
        $data['name'] = 'New name';

        $response = $this->putJson('/api/core/modules/'.$module->id, $data,  $this->getAuthHeaders());

        $response->assertStatus(200)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonFragment(['name' => 'New name']);
    }

    public function test_cannot_update_module_with_invalid_id(): void
    {
        $module = Module::factory()->createOne();
        
        $data = $module->toArray();
        $data['email'] = 'new.email@test.com';

        $response = $this->putJson('/api/core/modules/999999', $data,  $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertNotFound()
                ->assertJsonFragment(['success' => false]);
    }

    public function test_can_delete_module_by_id(): void
    {
        $module = Module::factory()->createOne();

        $response = $this->deleteJson('/api/core/modules/'.$module->id, [],  $this->getAuthHeaders());

        $response->assertNoContent();
    }

    public function test_cannot_delete_module_by_invalid_id(): void
    {
        $response = $this->deleteJson('/api/core/modules/999999', [],  $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertNotFound()
                ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_create_duplicated_module(): void
    {
        $module = Module::factory()->createOne();
        $data = $module->toArray();

        $response = $this->postJson('/api/core/modules/', $data, $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertUnprocessable()
                ->assertJsonIsObject()
                ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_update_module_duplicating_the_name(): void
    {
        $firstModule = Module::factory()->createOne();
        $secondModule = Module::factory()->createOne();
        
        $secondModule->name = $firstModule->name;
        $data = $secondModule->toArray();

        $response = $this->putJson('/api/core/modules/'.$secondModule->id, $data, $this->getAuthHeaders());

        $this->assertApiResponseStructureForError($response);
        $response->assertUnprocessable()
                ->assertJsonIsObject()
                ->assertJsonFragment(['success' => false]);
    }
}
