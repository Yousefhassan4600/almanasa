<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWebsiteLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, ['ar', 'en'], true)) {
            $referer = $request->headers->get('referer');
            $refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;
            $refererPath = $referer ? parse_url($referer, PHP_URL_PATH) : null;

            if ($refererHost === $request->getHost() && is_string($refererPath)) {
                $locale = explode('/', trim($refererPath, '/'))[0] ?? null;
            }
        }

        $locale = in_array($locale, ['ar', 'en'], true) ? $locale : $request->session()->get('website_locale', 'ar');
        $locale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';

        $request->attributes->set('website_locale', $locale);
        $request->session()->put('website_locale', $locale);
        app()->setLocale($locale);

        return $next($request);
    }
}
