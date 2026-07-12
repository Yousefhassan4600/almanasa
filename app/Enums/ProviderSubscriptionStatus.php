<?php

namespace App\Enums;

enum ProviderSubscriptionStatus: string
{
    case Pending = 'pending';
    case Trialing = 'trialing';
    case Active = 'active';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Trialing->value => 'Trialing',
            self::Active->value => 'Active',
            self::Suspended->value => 'Suspended',
            self::Expired->value => 'Expired',
            self::Cancelled->value => 'Cancelled',
        ];
    }

    public function canAccessProvider(): bool
    {
        return match ($this) {
            self::Trialing,
            self::Active => true,
            self::Pending,
            self::Suspended,
            self::Expired,
            self::Cancelled => false,
        };
    }
}
