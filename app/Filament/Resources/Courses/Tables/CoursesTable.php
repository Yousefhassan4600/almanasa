<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CoursesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('teacher_account_id')
                ->label('Teacher Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('accountSubject.name')
                ->label('Grade Subject')
                ->searchable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('slug')
                ->label('Slug')
                ->searchable()
                ->sortable(),
            TextColumn::make('thumbnail')
                ->label('Thumbnail')
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
