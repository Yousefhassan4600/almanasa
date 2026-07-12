<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AcademicYearStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
