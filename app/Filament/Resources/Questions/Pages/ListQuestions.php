<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Actions\ImportQuestionsFromExcelAction;
use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Questions\QuestionResource;

class ListQuestions extends BaseListRecords
{
    protected static string $resource = QuestionResource::class;

    protected function extraRecordActions(): array
    {
        return [
            ImportQuestionsFromExcelAction::make(),
        ];
    }
}
