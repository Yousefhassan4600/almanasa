<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Actions\Exams\GenerateExamModels;
use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Exams\ExamResource;

class CreateExam extends BaseCreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function afterCreate(): void
    {
        app(GenerateExamModels::class)->handle($this->record);
    }
}
