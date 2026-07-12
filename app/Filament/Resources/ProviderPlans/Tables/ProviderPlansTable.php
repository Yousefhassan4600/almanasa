<?php

namespace App\Filament\Resources\ProviderPlans\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderPlansTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table);
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->label('Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('price')
                ->label('Price')
                ->money(fn ($record): string => $record->currency_code)
                ->sortable(),
            TextColumn::make('billing_period_days')
                ->label('Billing Days')
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Active')
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
