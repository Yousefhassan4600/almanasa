<?php

namespace App\Enums;

enum CoursePeriodType: string
{
    case Term1 = 'term_1';
    case Term2 = 'term_2';
    case Yearly = 'yearly';

    public static function options(): array
    {
        return [
            self::Term1->value => 'Term 1',
            self::Term2->value => 'Term 2',
            self::Yearly->value => 'Yearly',
        ];
    }

    public function visiblePeriodTypes(): array
    {
        return match ($this) {
            self::Term1 => [self::Term1->value, self::Yearly->value],
            self::Term2 => [self::Term2->value, self::Yearly->value],
            self::Yearly => [self::Yearly->value],
        };
    }
}
