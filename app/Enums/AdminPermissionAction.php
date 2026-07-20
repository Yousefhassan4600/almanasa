<?php

namespace App\Enums;

enum AdminPermissionAction: string
{
    case ViewAll = 'viewAll';
    case ViewAny = 'viewAny';
    case ViewHis = 'viewHis';
    case Create = 'create';
    case Edit = 'edit';
    case Delete = 'delete';

    public function label(): string
    {
        return match ($this) {
            self::ViewAll => __('admin.View All'),
            self::ViewAny => __('admin.View Any'),
            self::ViewHis => __('admin.View His'),
            self::Create => __('admin.Create'),
            self::Edit => __('admin.Edit'),
            self::Delete => __('admin.Delete'),
        };
    }
}
