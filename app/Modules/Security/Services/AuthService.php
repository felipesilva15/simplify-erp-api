<?php

namespace App\Modules\Security\Services;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Core\Services\ActivityLogService;
use App\Modules\Security\DTO\AuthCredentialsDTO;
use App\Modules\Security\DTO\TokenDetailsDTO;
use App\Modules\Security\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Schema(
 *      schema="AccessTokenDTO",
 *      @OA\Property(property="access_token", type="string", example="token123"),
 *      @OA\Property(property="token_type", type="string", example="bearer"),
 *      @OA\Property(property="expires_in", type="number", example=3600)
 * )
 */
class AuthService {
    protected ActivityLogService $activity;

    public function __construct(ActivityLogService $activity) {
        $this->activity = $activity;
    }

    public function login(AuthCredentialsDTO $credentials): TokenDetailsDTO {
        $token = JWTAuth::attempt($credentials->toArray());

        if (!$token) {
            throw new InvalidCredentialsException();
        }

        $user = $this->getLoggedInUser();
        $this->activity->log($user, ActivityActionEnum::Auth, 'Realizou login');
    
        return $this->makeTokenDetailsDTO($token);
    }

    public function getLoggedInUser(): User {
        return Auth::user();
    }

    public function logout(): void {
        $user = $this->getLoggedInUser();
        $this->activity->log($user, ActivityActionEnum::Auth, 'Realizou logout');
        Auth::logout();
    }

    public function refreshToken(): TokenDetailsDTO {
        $token = Auth::refresh();
        $user = $this->getLoggedInUser();
        $this->activity->log($user, ActivityActionEnum::Auth, 'Atualizou o token de acesso');
        return $this->makeTokenDetailsDTO($token);
    }

    protected function makeTokenDetailsDTO(string $token): TokenDetailsDTO {
        return new TokenDetailsDTO(
            token: $token,
            type: 'bearer',
            expiresIn: Auth::factory()->getTTL() * 60
        );
    }

    public function hasAuthorized(User $user, string $permission): bool {
        return $user->hasPermission($permission);
    }
}