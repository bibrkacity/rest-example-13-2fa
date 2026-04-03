<?php

namespace App\Actions\Auth;

use App\Enums\VariableNames;
use App\Exceptions\AuthorizationException;
use Bibrkacity\SanctumSession\Services\SanctumSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Verify2faLogin
{
    public function handle(Request $request): JsonResponse
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
