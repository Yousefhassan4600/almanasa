<?php

namespace App\Filament\Resources\VideoProgress\Pages;

use App\Filament\Resources\VideoProgress\VideoProgressResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVideoProgress extends EditRecord
{
    protected static string $resource = VideoProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
