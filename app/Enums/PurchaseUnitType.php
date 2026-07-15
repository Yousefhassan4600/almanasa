<?php

namespace App\Enums;

enum PurchaseUnitType: string
{
    case Lesson = 'lesson';
    case Month = 'month';
    case Term = 'term';
    case Year = 'year';

    public static function options(): array
    {
        return [
            self::Lesson->value => 'Lesson',
            self::Month->value => 'Month',
            self::Term->value => 'Term',
            self::Year->value => 'Year',
        ];
    }
}
