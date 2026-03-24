<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\VariableNames;
use App\Exceptions\ApiException;
use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\Verify2faLoginRequest;
use App\Http\Requests\Auth\Verify2faRequest;
use App\Http\Responses\Enable2faResponse;
use App\Http\Responses\LoginResponse;
use App\Models\User;
use Bibrkacity\SanctumSession\Services\SanctumSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;
use PragmaRX\Google2FAQRCode\Google2FA;
use Symfony\Component\HttpFoundation\Response;
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
    public function login(LoginRequest $request): LoginResponse
    {

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        $query = User::query()
            ->where('email', $email);
        $user = $query->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new ApiException('Invalid login or password', ResponseAlias::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('start');

        return new LoginResponse($user, $token->plainTextToken);

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

        $user = $request->user();

        return new JsonResponse(data: ['data' => $user->toArray()], status: ResponseAlias::HTTP_OK, json: false);

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
            new OA\Response(response: ResponseAlias::HTTP_OK, description: 'Message Ok'),
        ]
    )]
    public function logout(): JsonResponse
    {

        $user = auth()->user();
        $user->tokens()->delete(); // Все устройства

        // $user->currentAccessToken()->delete(); //Только текущее устройство
        return new JsonResponse(data: ['data' => ['message' => 'Ok']], status: ResponseAlias::HTTP_OK, json: false);
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
    public function enable2fa(Request $request): Enable2faResponse
    {

        $user = $request->user();

        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secret;
        $user->save();

        $env = config('app.env');

        $title = $env == 'production'
            ? config('app.name')
            : config('app.name').'-'.substr($env, 0, 3);

        $qrCodeInline = $google2fa->getQRCodeInline(
            $title,
            $user->email,
            $secret
        );

        $qrUrl = $google2fa->getQRCodeUrl(
            $title,
            $user->email,
            $secret
        );

        return new Enable2faResponse($secret, $qrCodeInline, $qrUrl);

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
    public function verify2fa(Verify2faRequest $request): JsonResponse
    {
        $user = $request->user();

        $google2fa = new Google2FA();

        $secret = $user->google2fa_secret;

        $otp = str_replace(' ', '', $request->input('otp'));

        if ($google2fa->verifyKey($secret, $otp)) {
            $user->google2fa_enabled = User::ENABLED_2FA;
            $user->save();
            SanctumSession::put($request->bearerToken(), VariableNames::VERIFIED2FA->value, 'boolean', true);

            return new JsonResponse(['message' => '2FA enabled successfully'], ResponseAlias::HTTP_OK);
        } else {
            throw new AuthorizationException('Invalid otp', Response::HTTP_FORBIDDEN);
        }
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
    public function verify2faLogin(Verify2faLoginRequest $request): JsonResponse
    {

        $otp = str_replace(' ', '', $request->input('otp'));

        $user = $request->user();

        $secret = $user->google2fa_secret;

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($secret, $otp)) {

            SanctumSession::put($request->bearerToken(), VariableNames::VERIFIED2FA->value, 'boolean', true);

            return new JsonResponse(data: [
                'success' => true,
            ], status: ResponseAlias::HTTP_OK, json: false);
        } else {
            throw new AuthorizationException('Invalid OTP', Response::HTTP_FORBIDDEN);
        }
    }
}
