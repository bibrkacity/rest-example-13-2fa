<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\DestroyUser;
use App\Actions\User\IndexUser;
use App\Actions\User\ShowUser;
use App\Actions\User\StoreUser;
use App\Actions\User\UpdateUser;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\DestroyRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\ShowRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Responses\IndexResponse;
use App\Interfaces\IUserRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends ApiController
{
    public function __construct(
        private readonly IUserRepository $userRepository
    ) {
    }

    #[OA\Get(
        path: '/users',
        description: 'List of users',
        summary: 'List of users',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Users'],
        parameters: [

            new OA\Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    minimum: 1,
                ),
            ),

            new OA\Parameter(
                name: 'per_page',
                description: 'Count of users per page (0 - returns all filtered items)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    default: 20,
                    minimum: 0,
                ),
            ),

            new OA\Parameter(
                name: 'query',
                description: 'Query string for filters',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                ),
            ),

            new OA\Parameter(
                name: 'sort_name',
                description: 'Field name for sorting',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    default: 'name',
                    enum: ['name', 'email', 'created_at', 'updated_at'],
                ),
            ),

            new OA\Parameter(
                name: 'sort_dir',
                description: 'Direction of sorting of sort field (asc,desc)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    default: 'asc',
                    enum: ['asc', 'desc'],
                ),
            ),

        ],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'List of users by filters'),
        ]
    )]
    public function index(IndexRequest $request, IndexUser $action): IndexResponse
    {
        return $action->handle($request, $this->userRepository);
    }

    #[OA\Post(
        path: '/users',
        description: 'Add new user',
        summary: 'Add new user',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: [
                        'name',
                        'email',
                        'password',
                        'password_confirmation',
                    ],
                    properties: [
                        new OA\Property(
                            property: 'name',
                            description: 'User first name',
                            type: 'string',
                        ),

                        new OA\Property(
                            property: 'email',
                            description: 'User\'s email',
                            type: 'string',
                            format: 'email',
                        ),

                        new OA\Property(
                            property: 'password',
                            description: 'User\'s password',
                            type: 'string',
                        ),

                        new OA\Property(
                            property: 'password_confirmation',
                            description: 'User\'s password confirmation',
                            type: 'string',
                        ),

                    ]
                )
            )
        ),
        tags: ['Users'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_CREATED, description: 'Created user in JSON'),
        ]
    )]
    public function store(StoreRequest $request, StoreUser $action): JsonResponse
    {
        $data = $action->handle($request);

        return new JsonResponse(data: ['data' => $data], status: ResponseAlias::HTTP_CREATED, json: false);
    }

    #[OA\Get(
        path: '/users/{id}',
        description: 'One user details',
        summary: 'One user details',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Id of user',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    minimum: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'One user in JSON'),
            new OA\Response(response: ResponseAlias::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function show(ShowRequest $request, int $id, ShowUser $action): JsonResponse
    {
        $data = $action->handle($id);

        return new JsonResponse(data: ['data' => $data], status: ResponseAlias::HTTP_OK, json: false);
    }

    #[OA\Put(
        path: '/users/{id}',
        description: 'Update user',
        summary: 'Update user',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: [],
                    properties: [
                        new OA\Property(
                            property: 'name',
                            description: 'User full name',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'email',
                            description: 'User\'s email',
                            type: 'string',
                            format: 'email',
                        ),
                        new OA\Property(
                            property: 'password',
                            description: 'User\'s password',
                            type: 'string',
                            default: '',
                        ),
                        new OA\Property(
                            property: 'password_confirmation',
                            description: 'For password confirmation (required if password specified).',
                            type: 'string',
                            default: '',
                        ),
                    ]
                )
            )
        ),
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Id of the user',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    minimum: 1,
                ),
            ),
        ],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'Modified user in JSON'),
        ]
    )]
    public function update(UpdateRequest $request, int $id, UpdateUser $action): JsonResponse
    {
        $data = $action->handle($request, $id);

        return new JsonResponse(data: ['data' => $data], status: ResponseAlias::HTTP_OK, json: false);
    }

    #[OA\Delete(
        path: '/users/{id}',
        description: 'Delete of user (soft)',
        summary: 'Delete of user  (soft)',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Id of the User for delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                ),
            ),
        ],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_NO_CONTENT, description: 'Successfully deleted'),
        ]
    )]
    public function destroy(DestroyRequest $request, int $id, DestroyUser $action): JsonResponse
    {
        $action->handle($id);

        return new JsonResponse(data: null, status: ResponseAlias::HTTP_NO_CONTENT, json: false);
    }
}
