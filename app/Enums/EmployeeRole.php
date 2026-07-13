<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Support = 'support';

    public static function options(): array
    {
        return [
            self::Admin->value => 'Admin',
            self::Staff->value => 'Staff',
            self::Support->value => 'Support',
        ];
    }
}
