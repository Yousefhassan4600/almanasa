<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum MembershipStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Invited = 'invited';
    case Removed = 'removed';
}
