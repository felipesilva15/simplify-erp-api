<?php

namespace Tests\Feature;

use App\Modules\Security\Http\Middleware\JwtFromCookie;
use App\Modules\Security\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;
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

    private function getAdminCredentials(): array {
        return [
            'username' => $this->adminAuthUser->username,
            'password' => User::factory()->getDefaultPassword(),
        ];
    }

    private function loginAndGetCookie(array $data): Cookie
    {
        $cookieName = config('jwt.cookie_name');
        $response = $this->postJson("{$this->endpoint}/login", $data);

        $response->assertNoContent()
                ->assertPlainCookie($cookieName);

        return $response->getCookie($cookieName, false);
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

    public function test_can_login_with_auth_cookie(): void
    {
        config(['jwt.cookie_secure' => true]);
        
        $cookieName = config('jwt.cookie_name');

        $data = $this->getCredentials();
        $response = $this->postJson("{$this->endpoint}/login", $data);
        $cookie = $response->getCookie($cookieName, false);

        $response->assertNoContent()
                ->assertPlainCookie($cookieName)
                ->assertCookieNotExpired($cookieName);

        $this->assertNotEmpty($cookie->getValue());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isSecure());
    }

    public function test_cannot_get_auth_token_with_invalid_credentials(): void
    {
        $data = $this->getCredentials();
        $data['password'] = "123456789";
        $response = $this->postJson("{$this->endpoint}/token", $data);

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $this->withoutMiddleware(JwtFromCookie::class);

        $data = $this->getCredentials();
        $data['password'] = "123456789";
        $response = $this->postJson("{$this->endpoint}/login", $data);

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
        $cookieName = config('jwt.cookie_name');
        $cookie = $this->loginAndGetCookie($this->getAdminCredentials());

        Auth::forgetUser();

        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie($cookieName, $cookie->getValue())
            ->getJson("{$this->endpoint}/me");

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
        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie(config('jwt.cookie_name'), 'invalid_token')
            ->getJson("{$this->endpoint}/me");

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_expired_token_fails(): void
    {
        $cookieName = config('jwt.cookie_name');
        $cookie = $this->loginAndGetCookie($this->getCredentials());

        $this->travel(config('jwt.ttl'))->minutes();

        Auth::forgetUser();

        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie($cookieName, $cookie->getValue())
            ->getJson("{$this->endpoint}/me");

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_use_valid_token_successful(): void
    {
        $cookieName = config('jwt.cookie_name');
        $cookie = $this->loginAndGetCookie($this->getCredentials());

        Auth::forgetUser();

        $response = $this
            ->withCredentials()
            ->withUnencryptedCookie($cookieName, $cookie->getValue())
            ->getJson("{$this->endpoint}/me");

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonIsObject();
    }
}
