<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum QuestionType: string
{
    use HasOptions;

    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case TextAnswer = 'text_answer';
    case NumericAnswer = 'numeric_answer';
    case Essay = 'essay';
    case Matching = 'matching';
}
