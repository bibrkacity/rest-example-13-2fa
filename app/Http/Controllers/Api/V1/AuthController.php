<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\Enable2fa;
use App\Actions\Auth\Login;
use App\Actions\Auth\Verify2fa;
use App\Actions\Auth\Verify2faLogin;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\Verify2faLoginRequest;
use App\Http\Requests\Auth\Verify2faRequest;
use App\Http\Responses\Enable2faResponse;
use App\Http\Responses\LoginResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends ApiController
{
    #[OA\Post(
        path: '/login',
        description: 'Authorization and return API-token',
        summary: 'Authorization and return API-token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: [
                        'email',
                        'password',
                    ],
                    properties: [
                        new OA\Property(property: 'email', description: 'E-mail login', type: 'string', default: ''),
                        new OA\Property(property: 'password', description: 'Password  for login', type: 'string', default: ''),
                    ]
                )
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'API-token'),
        ]
    )]
    public function login(LoginRequest $request, Login $action): LoginResponse
    {
        return $action->handle($request);
    }

    #[OA\Get(
        path: '/auth/user',
        description: 'Info about current user',
        summary: 'User info',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'Current user'),
        ]
    )]
    public function getUser(Request $request): JsonResponse
    {

        return new JsonResponse(data: ['data' => $request->user()->toArray()], status: ResponseAlias::HTTP_OK, json: false);

    }

    #[OA\Get(
        path: '/auth/logout',
        description: 'Revoking current token',
        summary: 'Revoking current token',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_NO_CONTENT, description: 'Successfully logout'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return new JsonResponse(data: null, status: ResponseAlias::HTTP_NO_CONTENT, json: false);
    }

    #[OA\Post(
        path: '/auth/enable-2fa',
        description: 'Creating the QR-code for 2FA confirmation in the Google Authenticator',
        summary: 'Creating the QR-code for 2FA confirmation in the Google Authenticator',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'QR-code for confirmation 2fa in the Google Authenticator'),
        ]
    )]
    public function enable2fa(Request $request, Enable2fa $action): Enable2faResponse
    {
        return $action->handle($request);
    }

    #[OA\Post(
        path: '/auth/verify-2fa',
        description: 'Verify two-factor authentication for current user',
        summary: 'Verify two-factor authentication for current user',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: [
                        'otp',
                    ],
                    properties: [
                        new OA\Property(property: 'otp', description: 'One-time password', type: 'string', default: ''),
                    ]
                )
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: ResponseAlias::HTTP_OK, description: '2FA enabled successfully'),
        ]
    )]
    public function verify2fa(Verify2faRequest $request, Verify2fa $action): JsonResponse
    {
        return $action->handle($request);
    }

    #[OA\Post(
        path: '/auth/verify-2fa-login',
        description: 'Verify 2FA Login',
        summary: 'Verify two-factor authentication for login',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: [
                        'otp',
                    ],
                    properties: [
                        new OA\Property(
                            property: 'otp',
                            description: 'One-time password',
                            type: 'string',
                            default: ''
                        ),
                    ]
                )
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: ResponseAlias::HTTP_OK,
                description: 'API-token and 2FA status',
                content: new OA\MediaType(
                    mediaType: 'application/x-www-form-urlencoded',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(
                                property: 'success',
                                description: 'Is OTP successfully verified',
                                type: 'boolean',
                                example: true,
                            ),
                        ]
                    ),
                ),
            ),
        ]
    )]
    public function verify2faLogin(Verify2faLoginRequest $request, Verify2faLogin $action): JsonResponse
    {
        return $action->handle($request);

    }
}
