<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EnrollmentStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Suspended = 'suspended';
    case Refunded = 'refunded';
}
