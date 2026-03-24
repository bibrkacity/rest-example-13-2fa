<?php

namespace App\Http\Middleware;

use SanctumSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales');
        $defaultLocale = config('app.locale');
        $locale = $request->input('locale');

        if ($locale && in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
        } elseif (SanctumSession::has($request->bearerToken(), 'locale')) {
            $sessionLocale = SanctumSession::get($request->bearerToken(), 'locale');
            app()->setLocale(
                in_array($sessionLocale, $supportedLocales) ?
                    $sessionLocale :
                    $defaultLocale
            );
        } else {
            app()->setLocale($defaultLocale);
        }
        $locale = app()->getLocale();
        if ($locale === $defaultLocale) {
            SanctumSession::forget($request->bearerToken(), 'locale');
        } else {
            SanctumSession::put($request->bearerToken(), 'locale', 'string', $locale);
        }


        return $next($request);
    }
}
