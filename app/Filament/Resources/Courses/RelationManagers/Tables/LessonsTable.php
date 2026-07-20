<?php

namespace App\Filament\Resources\Courses\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->wrap(),
            TextColumn::make('coursePeriod.name')
                ->label(__('admin.labels.Period'))
                ->searchable(),
            TextColumn::make('starts_at')
                ->label(__('admin.labels.Starts At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label(__('admin.labels.Ends At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('num_of_video_views')
                ->label(__('admin.labels.Video Views'))
                ->numeric()
                ->sortable(),
            TextColumn::make('items_count')
                ->label(__('admin.labels.Items'))
                ->counts('items')
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label(__('admin.labels.Active')),
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
