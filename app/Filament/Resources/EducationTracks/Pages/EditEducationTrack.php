<?php

namespace App\Filament\Resources\EducationTracks\Pages;

use App\Filament\Resources\EducationTracks\EducationTrackResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationTrack extends EditRecord
{
    protected static string $resource = EducationTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
