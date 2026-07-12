<?php

namespace App\Filament\Resources\LessonProgress\Pages;

use App\Filament\Resources\LessonProgress\LessonProgressResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLessonProgress extends ManageRecords
{
    protected static string $resource = LessonProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
