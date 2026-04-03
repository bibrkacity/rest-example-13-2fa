<?php

namespace App\Actions\Auth;

use App\Enums\VariableNames;
use App\Exceptions\AuthorizationException;
use App\Models\User;
use Bibrkacity\SanctumSession\Services\SanctumSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Verify2fa
{
    public function handle(Request $request): JsonResponse
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
}
