<?php

namespace App\Filament\Resources\DownloadLogs\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\DownloadLogs\DownloadLogResource;

class ListDownloadLogs extends BaseListRecords
{
    protected static string $resource = DownloadLogResource::class;
}
