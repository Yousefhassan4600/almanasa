<?php

namespace App\Filament\Resources\StudentAnswers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class StudentAnswersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_attempt_id')
                ->label('Student Attempt Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('question_id')
                ->label('Question Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('question_option_id')
                ->label('Question Option Id')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_correct')
                ->label('Is Correct')
                ->boolean(),
            TextColumn::make('score')
                ->label('Score')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
