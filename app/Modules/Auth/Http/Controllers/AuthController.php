<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\Auth\LoginRequest;
use App\Modules\Auth\Actions\Auth\GetAuthUserAction;
use App\Modules\Auth\Actions\Auth\LoginAction;
use App\Modules\Auth\Actions\Auth\LogoutAction;
use App\Modules\Auth\Actions\Auth\RefreshTokenAction;
use App\Modules\Auth\DTO\AuthCredentialsDTO;
use App\Modules\Auth\Http\Resources\Auth\TokenDetailsResource;
use App\Modules\Auth\Http\Resources\User\UserResource;
use App\Modules\Auth\Services\AuthService;
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
     *      path="/api/auth/login",
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
    public function login(LoginRequest $request, LoginAction $action): JsonResponse {
        $dto = AuthCredentialsDTO::fromArray($request->validated());
        $tokenDetails = $action->execute($dto);

        return response()->json(new TokenDetailsResource($tokenDetails), 200);
    }
    
    /**
     * @OA\Get(
     *     path="/api/auth/me",
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
     *     path="/api/auth/logout",
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
        return response()->noContent();
    }
    
    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
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
