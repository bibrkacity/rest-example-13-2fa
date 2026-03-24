<?php

namespace App\Http\Middleware;

use App\Enums\VariableNames;
use App\Exceptions\AuthorizationException;
use Closure;
use Illuminate\Http\Request;
use SanctumSession;
use Symfony\Component\HttpFoundation\Response;

class Checking2fa
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->required2fa && (! SanctumSession::get($request->bearerToken(), VariableNames::VERIFIED2FA->value, false))) {
            throw new AuthorizationException('2FA is required for this action', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
