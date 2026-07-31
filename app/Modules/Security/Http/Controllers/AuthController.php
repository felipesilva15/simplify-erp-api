<?php

namespace App\Modules\Security\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Security\Http\Requests\Auth\LoginRequest;
use App\Modules\Security\DTO\AuthCredentialsDTO;
use App\Modules\Security\Http\Resources\Auth\TokenDetailsResource;
use App\Modules\Security\Http\Resources\User\UserResource;
use App\Modules\Security\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct(AuthService $service) {
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *      path="/api/security/auth/login",
     *      tags={"Authentication"},
     *      summary="Log in",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Credentials to log in",
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(
     *          response="204", 
     *          description="No content"
     *      ),
     *      @OA\Response(
     *         response="401", 
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     *  )
     * )
     */
    public function login(LoginRequest $request): Response {
        $dto = AuthCredentialsDTO::fromArray($request->validated());
        $tokenDetails = $this->service->login($dto);
        $cookie = Cookie::make(
            name: config('jwt.cookie_name'),
            value: $tokenDetails->token,
            minutes: config('jwt.ttl'),
            path: '/',
            domain: null,
            secure: config('jwt.cookie_secure'),
            httpOnly: true,
            raw: false,
            sameSite: config('jwt.cookie_same_site')
        );

        return response()
            ->noContent()
            ->cookie($cookie);
    }

    /**
     * @OA\Post(
     *      path="/api/security/auth/token",
     *      tags={"Authentication"},
     *      summary="Log in",
     *      @OA\RequestBody(
     *          required=true,
     *          description="Credentials to log in",
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Token details",
     *          @OA\JsonContent(ref="#/components/schemas/TokenDetailsResource")
     *      ),
     *      @OA\Response(
     *         response="401", 
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     *  )
     * )
     */
    public function token(LoginRequest $request): JsonResponse {
        $dto = AuthCredentialsDTO::fromArray($request->validated());
        $tokenDetails = $this->service->login($dto);

        return response()->json(new TokenDetailsResource($tokenDetails), 200);
    }
    
    /**
     * @OA\Get(
     *     path="/api/security/auth/me",
     *     tags={"Authentication"},
     *     summary="Logged in user data",
     *     @OA\Response(
     *         response="200", 
     *         description="User data",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response="401", 
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function me(): JsonResponse {
        $user = $this->service->getLoggedInUser();
        return response()->json(new UserResource($user), 200);
    }
    
    /**
     * @OA\Post(
     *     path="/api/security/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     @OA\Response(
     *          response="204", 
     *          description="No content"
     *      ),
     *     @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(): Response {
        $this->service->logout();
        return response()
            ->noContent()
            ->withoutCookie('token');
    }
    
    /**
     * @OA\Post(
     *     path="/api/security/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh the access token",
     *     @OA\Response(
     *          response="200", 
     *          description="Token details",
     *          @OA\JsonContent(ref="#/components/schemas/TokenDetailsResource")
     *      ),
     *     @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function refresh(): JsonResponse {
        $tokenDetails = $this->service->refreshToken();
        return response()->json(new TokenDetailsResource($tokenDetails), 200);
    }
}
