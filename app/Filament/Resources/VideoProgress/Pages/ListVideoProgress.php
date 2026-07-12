<?php

namespace App\Filament\Resources\VideoProgress\Pages;

use App\Filament\Resources\VideoProgress\VideoProgressResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoProgress extends ListRecords
{
    protected static string $resource = VideoProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
