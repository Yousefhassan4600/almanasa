<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AccessType: string
{
    use HasOptions;

    case Paid = 'paid';
    case Package = 'package';
    case Free = 'free';
    case Trial = 'trial';
    case Gift = 'gift';
    case AdminGranted = 'admin_granted';
}
