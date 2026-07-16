<?php

namespace App\Filament\Resources\LessonProgressStatusTypes\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonProgressStatusTypesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#'),
            TextColumn::make('name')
                ->label('Name')
                ->wrap(),
            TextColumn::make('slug')
                ->label('Slug')
                ->badge(),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'id';
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
