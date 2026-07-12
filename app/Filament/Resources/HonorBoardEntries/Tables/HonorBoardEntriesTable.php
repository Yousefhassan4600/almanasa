<?php

namespace App\Filament\Resources\HonorBoardEntries\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class HonorBoardEntriesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('grade_name')
                ->label('Grade Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('score_percentage')
                ->label('Score Percentage')
                ->searchable()
                ->sortable(),
            TextColumn::make('rank_label')
                ->label('Rank Label')
                ->searchable()
                ->sortable(),
            TextColumn::make('image')
                ->label('Image')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
