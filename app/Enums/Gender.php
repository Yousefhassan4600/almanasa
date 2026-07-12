<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public static function options(): array
    {
        return [
            self::Male->value => 'Male',
            self::Female->value => 'Female',
        ];
    }
}
