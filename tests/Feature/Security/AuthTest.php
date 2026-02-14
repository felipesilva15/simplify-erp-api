<?php

namespace Tests\Feature;

use App\Modules\Security\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected string $endpoint = '/api/security/auth';

    private function getCredentials(): array {
        $user = User::factory()->createOne();

        return [
            'username' => $user->username,
            'password' => User::factory()->getDefaultPassword(),
        ];
    }

    public function test_can_get_auth_token(): void
    {
        $data = $this->getCredentials();
        $response = $this->postJson("{$this->endpoint}/token", $data);

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonIsObject()
                ->assertJsonStructure([
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);
    }

    public function test_cannot_get_auth_token_with_invalid_credentials(): void
    {
        $data = $this->getCredentials();
        $data['password'] = "123456789";
        $response = $this->postJson("{$this->endpoint}/token", $data);

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_logout(): void
    {
        $response = $this->postJson("{$this->endpoint}/logout", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();
    }

    public function test_can_refresh_token(): void
    {
        $response = $this->postJson("{$this->endpoint}/refresh", [], $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonIsObject()
                ->assertJsonStructure([
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);
    }

    public function test_can_get_data_logged_in_user(): void
    {
        $response = $this->getJson("{$this->endpoint}/me", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonIsObject()
                ->assertJsonStructure([
                    'id',
                    'name',
                    'username',
                    'permissions'
                ])
                ->assertJsonFragment([
                    'id' => $this->adminAuthUser->id,
                    'name' => $this->adminAuthUser->name,
                    'username' => $this->adminAuthUser->username,
                ]);
    }

    public function test_invalid_token_fails(): void
    {
        $headers = [
            'Authorization' => 'Bearer invalid_token'
        ];

        $response = $this->getJson("{$this->endpoint}/me", $headers);

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_expired_token_fails(): void
    {
        $data = $this->getCredentials();
        $response = $this->postJson("{$this->endpoint}/token", $data);

        $expiresIn = $response->json('expires_in');
        $token = $response->json('access_token');

        $this->travel($expiresIn)->minutes();

        Auth::forgetUser();

        $response = $this->getJson("{$this->endpoint}/me", [
            "Authorization" => "Bearer ".$token
        ]);

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_use_valid_token_successful(): void
    {
        $data = $this->getCredentials();
        $response = $this->postJson("{$this->endpoint}/token", $data);

        $token = $response->json('access_token');

        Auth::forgetUser();

        $response = $this->getJson("{$this->endpoint}/me", [
            "Authorization" => "Bearer ".$token
        ]);

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonIsObject();
    }
}
