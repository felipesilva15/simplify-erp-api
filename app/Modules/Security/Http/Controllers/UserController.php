<?php

namespace App\Modules\Security\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Security\Http\Requests\User\StoreUserRequest;
use App\Modules\Security\Http\Requests\User\UpdateUserRequest;
use App\Modules\Security\Http\Resources\User\UserResource;
use App\Modules\Security\Http\Resources\User\UserCollection;
use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\UserService;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service) {
        $this->service = $service;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * @OA\Get(
     *      path="/api/security/users",
     *      tags={"User"},
     *      summary="List all rows",
     *      @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="email", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="email_verified_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="password", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="remember_token", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="created_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="updated_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="username", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="phone_number", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="deleted_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="sort_by[]", in="query", description="Fields name's to sort", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="id"))),
     *      @OA\Parameter(name="sort_dir[]", in="query", description="Directions to sort: Ascending = 'asc'; Descending = 'desc'", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="desc"))),
     *      @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response="200", 
     *          description="User list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/UserCollection")
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListRequest $request): JsonResponse {
        $serviceResult = $this->service->list($request->all());

        $paginated = new UserCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/security/users/{id}",
     *      tags={"User"},
     *      summary="List a user by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="User data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/UserResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(User $user): JsonResponse {
        $serviceResult = $this->service->show($user);

        return $this->success(
            data: new UserResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/security/users",
     *      tags={"User"},
     *      summary="Registers a user",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new user",
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered user data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/UserResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse {
        $dto = UserDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new UserResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/security/users/{id}/edit",
     *      tags={"User"},
     *      summary="Get data to edit a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="User data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/UserResource"
     *                      ),
     *                      @OA\Property(property="warnings", type="array", @OA\Items(type="string", example="Este recurso não pode ser editado."), nullable=true),
     *                      @OA\Property(
     *                          property="meta", 
     *                          type="object", 
     *                          @OA\Property(property="editable", type="boolean", example=true)
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      )
     * )
     */
    public function edit(User $user): JsonResponse {
        $serviceResult = $this->service->edit($user);

        return $this->success(
            data: new UserResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/security/users/{id}",
     *      tags={"User"},
     *      summary="Update a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update user",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated user data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/UserResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(User $user, UpdateUserRequest $request): JsonResponse {
        $dto = UserDTO::fromArray($request->validated());
        $dto->fieldsToUse = array_keys($request->validated());
        $serviceResult = $this->service->update($user, $dto);

        return $this->success(
            data: new UserResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/security/users/{id}",
     *      tags={"User"},
     *      summary="Delete a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="204", 
     *          description="No content"
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(User $user): Response {
        $this->service->delete($user);
        return response()->noContent();
    }
}