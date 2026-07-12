<?php

namespace App\Filament\Resources\LessonItems\Pages;

use App\Filament\Resources\LessonItems\LessonItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLessonItems extends ManageRecords
{
    protected static string $resource = LessonItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
