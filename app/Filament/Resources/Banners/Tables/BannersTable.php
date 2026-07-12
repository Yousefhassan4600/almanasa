<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class BannersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('subtitle')
                ->label('Subtitle')
                ->searchable()
                ->sortable(),
            TextColumn::make('image')
                ->label('Image')
                ->searchable()
                ->sortable(),
            TextColumn::make('button_text')
                ->label('Button Text')
                ->searchable()
                ->sortable(),
            TextColumn::make('button_url')
                ->label('Button Url')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
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
