<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class GeneralSettingsCache
{
    private const KEY_PREFIX = 'api.general.settings';

    private const SHAPE_VERSION = 2;

    private const VERSION_KEY = 'api.general.settings.version';

    private const LOCALES = ['en', 'ar', 'ur'];

    public static function key(?string $locale = null): string
    {
        return self::for('settings', locale: $locale);
    }

    public static function for(string $endpoint, array $parameters = [], ?string $locale = null): string
    {
        ksort($parameters);

        $parameterHash = $parameters ? '.' . md5(json_encode($parameters)) : '';

        return self::KEY_PREFIX
            . '.s' . self::SHAPE_VERSION
            . '.v' . self::version()
            . '.' . ($locale ?: app()->getLocale())
            . '.' . $endpoint
            . $parameterHash;
    }

    public static function forget(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    private static function version(): int
    {
        $version = Cache::get(self::VERSION_KEY);

        if (! $version) {
            $version = 1;
            Cache::forever(self::VERSION_KEY, $version);
        }

        return (int) $version;
    }
}
