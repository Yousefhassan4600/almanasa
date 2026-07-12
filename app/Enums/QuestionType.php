<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case Written = 'written';

    public static function options(): array
    {
        return [
            self::SingleChoice->value => 'Single Choice',
            self::MultipleChoice->value => 'Multiple Choice',
            self::TrueFalse->value => 'True False',
            self::Written->value => 'Written',
        ];
    }
}
