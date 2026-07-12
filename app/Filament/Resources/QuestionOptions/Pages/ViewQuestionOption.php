<?php

namespace App\Filament\Resources\QuestionOptions\Pages;

use App\Filament\Resources\QuestionOptions\QuestionOptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionOption extends ViewRecord
{
    protected static string $resource = QuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
