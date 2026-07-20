<?php

namespace App\Filament\Resources\CoursePeriods\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class CoursePeriodsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('name')
                ->label(__('admin.labels.Name'))
                ->wrap(),
            TextColumn::make('type')
                ->label(__('admin.labels.Type'))
                ->badge(),
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

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return false;
    }
}
