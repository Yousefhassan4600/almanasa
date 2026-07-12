<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Rejected = 'rejected';

    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Active->value => 'Active',
            self::Suspended->value => 'Suspended',
            self::Rejected->value => 'Rejected',
        ];
    }
}
