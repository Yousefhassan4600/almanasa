<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TenantRole: string
{
    use HasOptions;

    case Owner = 'owner';
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';
    case Support = 'support';
}
