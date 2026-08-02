<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');
        $queryLocale = $request->get('lang');
        $headerLocale = $request->header('Accept-Language');
        $sessionLocale = session('locale');

        $locale = $this->resolveLocale($routeLocale)
            ?? $this->resolveLocale($queryLocale)
            ?? $this->resolveLocale($headerLocale)
            ?? $this->resolveLocale($sessionLocale)
            ?? 'en';

        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(?string $locale): ?string
    {
        if (! $locale) {
            return null;
        }

        $normalized = strtolower(substr(trim($locale), 0, 2));

        return in_array($normalized, ['ar', 'en'], true) ? $normalized : null;
    }
}
