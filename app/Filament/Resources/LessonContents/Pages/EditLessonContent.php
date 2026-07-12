<?php

namespace App\Filament\Resources\LessonContents\Pages;

use App\Filament\Resources\LessonContents\LessonContentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLessonContent extends EditRecord
{
    protected static string $resource = LessonContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
