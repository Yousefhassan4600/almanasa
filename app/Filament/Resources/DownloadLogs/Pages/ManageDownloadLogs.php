<?php

namespace App\Filament\Resources\DownloadLogs\Pages;

use App\Filament\Resources\DownloadLogs\DownloadLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDownloadLogs extends ManageRecords
{
    protected static string $resource = DownloadLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
