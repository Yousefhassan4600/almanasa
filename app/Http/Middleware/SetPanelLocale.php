<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetPanelLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! class_exists('Dom\HTMLDocument')) {
            // Fallback for older PHP versions
            error_log('Warning: PHP version does not support Dom\HTMLDocument');
        }

        $locale = $request->session()->get('locale')
            ?? $request->cookie('filament_language_switcher_locale')
            ?? config('app.locale', 'ar');

        App::setLocale($locale);

        return $next($request);
    }
}
