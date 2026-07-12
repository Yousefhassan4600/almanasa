<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case NotStarted = 'not_started';
    case Watching = 'watching';
    case Completed = 'completed';

    public static function options(): array
    {
        return [
            self::NotStarted->value => 'Not Started',
            self::Watching->value => 'Watching',
            self::Completed->value => 'Completed',
        ];
    }
}
