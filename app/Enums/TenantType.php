<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TenantType: string
{
    use HasOptions;

    case Academy = 'academy';
    case StandaloneTeacher = 'standalone_teacher';
}
