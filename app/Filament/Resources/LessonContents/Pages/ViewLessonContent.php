<?php

namespace App\Filament\Resources\LessonContents\Pages;

use App\Filament\Resources\LessonContents\LessonContentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLessonContent extends ViewRecord
{
    protected static string $resource = LessonContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
