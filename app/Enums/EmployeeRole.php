<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';
    case Staff = 'staff';
    case Support = 'support';

    public static function options(): array
    {
        return [
            self::Owner->value => 'Owner',
            self::Admin->value => 'Admin',
            self::Teacher->value => 'Teacher',
            self::Student->value => 'Student',
            self::Parent->value => 'Parent',
            self::Staff->value => 'Staff',
            self::Support->value => 'Support',
        ];
    }
}
