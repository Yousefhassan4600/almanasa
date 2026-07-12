<?php

namespace App\Filament\Resources\QuestionOptions\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class QuestionOptionsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('question_id')
                ->label('Question Id')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_correct')
                ->label('Is Correct')
                ->boolean(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
