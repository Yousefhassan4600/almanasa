<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\AuditLogs\AuditLogResource;

class ListAuditLogs extends BaseListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
