<?php

namespace App\Filament\Resources\StudentAttempts\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class StudentAttemptsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('attemptable_type')
                ->label('Attemptable Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('attemptable_id')
                ->label('Attemptable Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('attempt_number')
                ->label('Attempt Number')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
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
