<?php

namespace App\Filament\Resources\VideoProgress\Pages;

use App\Filament\Resources\VideoProgress\VideoProgressResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVideoProgress extends ViewRecord
{
    protected static string $resource = VideoProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
