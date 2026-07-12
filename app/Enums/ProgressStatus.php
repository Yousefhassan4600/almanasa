<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ProgressStatus: string
{
    use HasOptions;

    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
