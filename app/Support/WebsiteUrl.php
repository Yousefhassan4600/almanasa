<?php

namespace App\Support;

use Illuminate\Support\Str;

class WebsiteUrl
{
    public static function locale(): string
    {
        $locale = request()->route('locale') ?? request()->attributes->get('website_locale') ?? session('website_locale');

        return in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    }

    public static function path(string $path = '/', ?string $locale = null): string
    {
        $locale ??= self::locale();
        $path = '/'.ltrim($path, '/');

        if (Str::startsWith($path, ['/ar/', '/en/', '/ar?', '/en?']) || in_array($path, ['/ar', '/en'], true)) {
            $path = preg_replace('#^/(?:ar|en)(?=/|\?|$)#', '', $path) ?: '/';
        }

        return '/'.$locale.($path === '/' ? '' : $path);
    }
}
