<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Base\Pages\BaseViewRecord;
use App\Filament\Resources\AuditLogs\AuditLogResource;

class ViewAuditLog extends BaseViewRecord
{
    protected static string $resource = AuditLogResource::class;
}
