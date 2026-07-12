<?php

namespace App\Filament\Resources\StudentAnswers\Pages;

use App\Filament\Resources\StudentAnswers\StudentAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentAnswers extends ManageRecords
{
    protected static string $resource = StudentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
