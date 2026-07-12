<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Blocked = 'blocked';

    public static function options(): array
    {
        return [
            self::Active->value => 'Active',
            self::Inactive->value => 'Inactive',
            self::Blocked->value => 'Blocked',
        ];
    }
}
