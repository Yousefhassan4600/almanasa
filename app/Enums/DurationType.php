<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DurationType: string
{
    use HasOptions;

    case Days = 'days';
    case Months = 'months';
    case Term = 'term';
    case AcademicYear = 'academic_year';
    case Lifetime = 'lifetime';
}
