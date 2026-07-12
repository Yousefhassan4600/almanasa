<?php

namespace App\Filament\Resources\ChatRooms\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ChatRoomsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('lesson_id')
                ->label('Lesson Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
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
