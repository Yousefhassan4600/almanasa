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
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable(),
            TextColumn::make('planOption.plan.name')
                ->label('Plan')
                ->searchable(),
            TextColumn::make('planOption.billing_period_days')
                ->label('Billing Days')
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->badge()
                ->sortable(),
            TextColumn::make('amount')
                ->label('Amount')
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
