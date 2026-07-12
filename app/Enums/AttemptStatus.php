<?php

namespace App\Enums;

enum AttemptStatus: string
{
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Graded = 'graded';
    case Cancelled = 'cancelled';

    public static function options(): array
    {
        return [
            self::InProgress->value => 'In Progress',
            self::Submitted->value => 'Submitted',
            self::Graded->value => 'Graded',
            self::Cancelled->value => 'Cancelled',
        ];
    }
}
