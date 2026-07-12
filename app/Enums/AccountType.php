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

    public static function options(): array
    {
        return [
            self::SaasOwner->value => 'Owner Of SaaS',
            self::Academy->value => 'Academy',
            self::AcademyTeacher->value => 'Academy Teacher',
            self::StandaloneTeacher->value => 'Standalone Teacher',
            self::Student->value => 'Student',
            self::Parent->value => 'Parent',
        ];
    }

    public function canAccessDashboard(): bool
    {
        return match ($this) {
            self::SaasOwner,
            self::Academy,
            self::AcademyTeacher,
            self::StandaloneTeacher => true,
            self::Student,
            self::Parent => false,
        };
    }

    public function canAccessWebsite(): bool
    {
        return match ($this) {
            self::Student,
            self::Parent => true,
            self::SaasOwner,
            self::Academy,
            self::AcademyTeacher,
            self::StandaloneTeacher => false,
        };
    }

    public function canCreateSubAccounts(): bool
    {
        return match ($this) {
            self::SaasOwner,
            self::Academy,
            self::StandaloneTeacher => true,
            self::AcademyTeacher,
            self::Student,
            self::Parent => false,
        };
    }
}
