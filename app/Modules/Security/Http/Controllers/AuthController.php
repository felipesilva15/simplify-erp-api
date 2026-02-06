<?php

namespace App\Modules\Security\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Security\Http\Requests\Auth\LoginRequest;
use App\Modules\Security\Actions\Auth\GetAuthUserAction;
use App\Modules\Security\Actions\Auth\LoginAction;
use App\Modules\Security\Actions\Auth\LogoutAction;
use App\Modules\Security\Actions\Auth\RefreshTokenAction;
use App\Modules\Security\DTO\AuthCredentialsDTO;
use App\Modules\Security\Http\Resources\Auth\TokenDetailsResource;
use App\Modules\Security\Http\Resources\User\UserResource;
use App\Modules\Security\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
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
    public function login(LoginRequest $request, LoginAction $action): Response {
        $dto = AuthCredentialsDTO::fromArray($request->validated());
        $tokenDetails = $action->execute($dto);

        return response()
            ->noContent()
            ->cookie('token',
                $tokenDetails->token,
                config('jwt.ttl'),
                '/',
                null,
                true,
                true,
                false,
                'Lax'
            );
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
    public function token(LoginRequest $request, LoginAction $action): JsonResponse {
        $dto = AuthCredentialsDTO::fromArray($request->validated());
        $tokenDetails = $action->execute($dto);

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
    public function me(GetAuthUserAction $action): JsonResponse {
        $user = $action->execute();
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
    public function logout(LogoutAction $action): Response {
        $action->execute();
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
    public function refresh(RefreshTokenAction $action): JsonResponse {
        $tokenDetails = $action->execute();
        return response()->json(new TokenDetailsResource($tokenDetails), 200);
    }
}
