<?php

namespace App\Actions\Auth;

use App\Http\Responses\Enable2faResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;

class Enable2fa
{
    public function handle(Request $request): Enable2faResponse
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
}
