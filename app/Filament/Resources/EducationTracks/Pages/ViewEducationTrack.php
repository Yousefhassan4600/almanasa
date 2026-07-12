<?php

namespace App\Filament\Resources\EducationTracks\Pages;

use App\Filament\Resources\EducationTracks\EducationTrackResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEducationTrack extends ViewRecord
{
    protected static string $resource = EducationTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
