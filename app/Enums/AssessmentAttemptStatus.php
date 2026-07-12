<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AssessmentAttemptStatus: string
{
    use HasOptions;

    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Graded = 'graded';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
