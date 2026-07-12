<?php

namespace App\Enums;

enum AccountType: string
{
    case SaasOwner = 'saas_owner';
    case Academy = 'academy';
    case AcademyTeacher = 'academy_teacher';
    case StandaloneTeacher = 'standalone_teacher';
    case Student = 'student';
    case Parent = 'parent';
    case Employee = 'employee';

    public static function options(): array
    {
        return [
            self::SaasOwner->value => 'Owner Of SaaS',
            self::Academy->value => 'Academy',
            self::AcademyTeacher->value => 'Academy Teacher',
            self::StandaloneTeacher->value => 'Standalone Teacher',
            self::Student->value => 'Student',
            self::Parent->value => 'Parent',
            self::Employee->value => 'Employee',
        ];
    }

    public function canAccessDashboard(): bool
    {
        return match ($this) {
            self::SaasOwner => true,
            self::Academy => true,
            self::AcademyTeacher => true,
            self::StandaloneTeacher => true,
            self::Student => false,
            self::Parent => false,
            self::Employee => true,
        };
    }

    public function canAccessWebsite(): bool
    {
        return match ($this) {
            self::Student => true,
            self::Parent => true,
            self::Employee => false,
            self::SaasOwner => false,
            self::Academy => false,
            self::AcademyTeacher => false,
            self::StandaloneTeacher => false,
        };
    }

    public function canCreateSubAccounts(): bool
    {
        return match ($this) {
            self::SaasOwner => true,
            self::Academy => true,
            self::StandaloneTeacher => true,
            self::AcademyTeacher => false,
            self::Student => false,
            self::Parent => false,
            self::Employee => false,
        };
    }
}
