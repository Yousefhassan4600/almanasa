<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum QuestionDifficulty: string
{
    use HasOptions;

    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';
}
