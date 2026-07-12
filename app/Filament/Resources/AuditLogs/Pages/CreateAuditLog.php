<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\AuditLogs\AuditLogResource;

class CreateAuditLog extends BaseCreateRecord
{
    protected static string $resource = AuditLogResource::class;
}
