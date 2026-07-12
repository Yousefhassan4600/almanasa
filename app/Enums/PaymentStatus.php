<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Rejected = 'rejected';
    case Refunded = 'refunded';

    public static function options(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Paid->value => 'Paid',
            self::Failed->value => 'Failed',
            self::Rejected->value => 'Rejected',
            self::Refunded->value => 'Refunded',
        ];
    }
}
