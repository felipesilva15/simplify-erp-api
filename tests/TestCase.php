<?php

namespace Tests;

use App\Modules\Security\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected string $authToken;
    protected User $authUser;

    protected function setUp(): void {
        parent::setUp();

        $this->generateAndSetAuthData();
    }

    protected function generateAndSetAuthData(): void {
        $this->authUser = User::factory()->admin()->createOne();
        $this->authToken = JWTAuth::fromUser($this->authUser);
    }

    protected function getAuthHeaders() : array {
        return [
            "Authorization" => "Bearer {$this->authToken}"
        ];
    }

    protected function assertApiResponseStructure(TestResponse $response)
    {
        $response->assertJsonIsObject()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'warnings',
                'links',
                'errors',
                'meta',
            ]);
    }

    protected function assertApiResponseStructureForListing(TestResponse $response)
    {
        $response->assertJsonIsObject()
            ->assertJsonStructure([
                'data',
                'links' => [
                    'first',
                    'previous',
                    'next',
                    'last'
                ],
                'meta' => [
                    'per_page',
                    'current_page',
                    'last_page',
                    'total'
                ],
            ]);
    }

    protected function assertApiResponseStructureForError(TestResponse $response)
    {
        $response->assertJsonIsObject()
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }
}
