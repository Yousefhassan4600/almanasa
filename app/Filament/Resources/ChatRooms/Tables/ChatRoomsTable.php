<?php

namespace App\Filament\Resources\ChatRooms\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ChatRoomsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label(__('admin.labels.Provider Id'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label(__('admin.labels.Course Id'))
                ->searchable()
                ->sortable(),
            TextColumn::make('lesson_id')
                ->label(__('admin.labels.Lesson Id'))
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label(__('admin.labels.Is Active'))
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
