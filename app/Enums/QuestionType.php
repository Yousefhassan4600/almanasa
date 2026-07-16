<?php

namespace App\Enums;

enum QuestionType: string
{
    case Mcq = 'mcq';
    case TrueFalse = 'tf';
    case Statement = 'statement';

    public static function options(): array
    {
        return [
            self::Mcq->value => 'MCQ',
            self::TrueFalse->value => 'True / False',
            self::Statement->value => 'Statement',
        ];
    }
}
