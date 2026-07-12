<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Active->value => 'Active',
            self::Expired->value => 'Expired',
            self::Cancelled->value => 'Cancelled',
        ];
    }
}
