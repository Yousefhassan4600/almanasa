<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TenantStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Deleted = 'deleted';
}
