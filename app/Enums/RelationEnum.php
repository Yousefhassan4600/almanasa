<?php

namespace App\Enums;

enum RelationEnum: string
{
    case Father = 'father';
    case Mother = 'mother';
    case Guardian = 'guardian';
    case Other = 'other';

    public static function options(): array
    {
        return [
            self::Father->value => 'Father',
            self::Mother->value => 'Mother',
            self::Guardian->value => 'Guardian',
            self::Other->value => 'Other',
        ];
    }
}
