<?php

namespace App\Enums;

enum QuestionDifficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public static function options(): array
    {
        return [
            self::Easy->value => 'Easy',
            self::Medium->value => 'Medium',
            self::Hard->value => 'Hard',
        ];
    }
}
