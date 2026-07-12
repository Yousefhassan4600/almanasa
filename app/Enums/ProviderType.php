<?php

namespace App\Enums;

enum ProviderType: string
{
    case Academy = 'academy';
    case StandaloneTeacher = 'standalone_teacher';

    public static function options(): array
    {
        return [
            self::Academy->value => 'Academy',
            self::StandaloneTeacher->value => 'Standalone Teacher',
        ];
    }
}
