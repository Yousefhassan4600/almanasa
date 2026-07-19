<?php

namespace App\Filament\Resources\Lessons\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonItemsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->searchable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->wrap(),
            TextColumn::make('type')
                ->label('Type')
                ->badge()
                ->wrap(),
            TextColumn::make('assignment.title')
                ->label('Assignment')
                ->badge(),
            TextColumn::make('exam.title')
                ->label('Exam')
                ->badge(),
            TextColumn::make('duration_minutes')
                ->label('Duration Minutes')
                ->badge()
                ->sortable(),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label('Ends At')
                ->dateTime()
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label('Active'),
            ToggleColumn::make('is_free')
                ->label('Free'),
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
}
