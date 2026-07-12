<?php

namespace App\Enums;

enum ChatMemberRole: string
{
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';
    case Admin = 'admin';

    public static function options(): array
    {
        return [
            self::Teacher->value => 'Teacher',
            self::Student->value => 'Student',
            self::Parent->value => 'Parent',
            self::Admin->value => 'Admin',
        ];
    }
}
