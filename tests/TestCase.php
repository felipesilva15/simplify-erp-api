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

    protected string $adminAuthToken;
    protected string $commomUserAuthToken;
    protected User $adminAuthUser;
    protected User $commomUserAuthUser;

    protected function setUp(): void {
        parent::setUp();

        $this->generateAndSetAuthData();
    }

    protected function generateAndSetAuthData(): void {
        $this->adminAuthUser = User::factory()->admin()->createOne();
        $this->commomUserAuthUser = User::factory()->createOne();
        $this->adminAuthToken = JWTAuth::fromUser($this->adminAuthUser);
        $this->commomUserAuthToken = JWTAuth::fromUser($this->commomUserAuthUser);
    }

    protected function getCommomUserAuthHeaders() : array {
        return [
            "Authorization" => "Bearer {$this->commomUserAuthToken}"
        ];
    }

    protected function getAdminAuthHeaders() : array {
        return [
            "Authorization" => "Bearer {$this->adminAuthToken}"
        ];
    }

    protected function assertApiResponseStructureForListing(TestResponse $response)
    {
        $response->assertJsonIsObject()
            ->assertJsonStructure([
                'success',
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

    protected function assertErrorResponse(TestResponse $response, int $status)
    {
        $response->assertStatus($status)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'success',
                'message'
            ])
            ->assertJsonFragment(['success' => false]);
    }
}
