<?php

namespace App\Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Modules\Auth\Actions\User\StoreUserAction;
use App\Modules\Auth\Actions\User\UpdateUserAction;
use App\Modules\Auth\Actions\User\DeleteUserAction;
use App\Modules\Auth\Actions\User\EditUserAction;
use App\Modules\Auth\Actions\User\ShowUserAction;
use App\Modules\Auth\Actions\User\ListUserAction;
use App\Modules\Auth\Http\Requests\User\StoreUserRequest;
use App\Modules\Auth\Http\Requests\User\UpdateUserRequest;
use App\Modules\Auth\Http\Requests\User\ListUserRequest;
use App\Modules\Auth\Http\Resources\User\UserResource;
use App\Modules\Auth\Http\Resources\User\UserCollection;
use App\Modules\Auth\DTO\UserDTO;

class UserController
{
    /**
     * @OA\Get(
     *      path="/api/auth/users",
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
     *          @OA\JsonContent(ref="#/components/schemas/UserCollection")
     *      )
     * )
     */
    public function index(ListUserRequest $request, ListUserAction $action): JsonResponse {
        $userList = $action->execute($request->all());

        return response()->json(new UserCollection($userList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/users/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function show(int $id, ShowUserAction $action): JsonResponse {
        $user = $action->execute($id);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/users",
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
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreUserRequest $request, StoreUserAction $action): JsonResponse {
        $dto = UserDTO::fromArray($request->validated());
        $user = $action->execute($dto);

        return response()->json(new UserResource($user), 201);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/users/{id}/edit",
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
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function edit(int $id, EditUserAction $action): JsonResponse {
        $user = $action->execute($id);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/auth/users/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/UserResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(int $id, UpdateUserRequest $request, UpdateUserAction $action): JsonResponse {
        $dto = UserDTO::fromArray($request->validated());
        $user = $action->execute($id, $dto);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/auth/users/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(int $id, DeleteUserAction $action): Response {
        $action->execute($id);
        return response()->noContent();
    }
}