<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\AuditLogs\AuditLogResource;

class EditAuditLog extends BaseEditRecord
{
    protected static string $resource = AuditLogResource::class;
}
