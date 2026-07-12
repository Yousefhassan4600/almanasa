<?php

namespace App\Filament\Resources\ProviderSubscriptions\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderSubscriptionsTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table);
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable(),
            TextColumn::make('plan.name')
                ->label('Plan')
                ->searchable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            TextColumn::make('amount')
                ->label('Amount')
                ->money(fn ($record): string => $record->currency_code)
                ->sortable(),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label('Ends At')
                ->dateTime()
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
