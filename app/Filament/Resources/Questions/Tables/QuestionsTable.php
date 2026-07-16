<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class QuestionsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->wrap(),
            TextColumn::make('lesson.course.title')
                ->label('Course')
                ->searchable()
                ->wrap(),
            TextColumn::make('lesson.title')
                ->label('Lesson')
                ->searchable()
                ->wrap(),
            TextColumn::make('type')
                ->label('Type')
                ->badge()
                ->sortable(),
            TextColumn::make('difficulty')
                ->label('Difficulty')
                ->badge()
                ->sortable(),
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
