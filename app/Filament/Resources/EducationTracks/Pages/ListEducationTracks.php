<?php

namespace App\Filament\Resources\EducationTracks\Pages;

use App\Filament\Resources\EducationTracks\EducationTrackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEducationTracks extends ListRecords
{
    protected static string $resource = EducationTrackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
