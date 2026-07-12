<?php

namespace App\Filament\Resources\StudentAnswers\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\StudentAnswers\StudentAnswerResource;

class ListStudentAnswers extends BaseListRecords
{
    protected static string $resource = StudentAnswerResource::class;
}
