<?php

namespace App\Filament\Resources\LessonContents\Pages;

use App\Filament\Resources\LessonContents\LessonContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLessonContents extends ListRecords
{
    protected static string $resource = LessonContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
