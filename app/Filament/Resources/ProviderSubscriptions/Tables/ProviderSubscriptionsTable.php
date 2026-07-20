<?php

namespace App\Filament\Resources\ProviderSubscriptions\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
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
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable(),
            TextColumn::make('planOption.plan.name')
                ->label(__('admin.labels.Plan'))
                ->searchable(),
            TextColumn::make('planOption.billing_period_days')
                ->label(__('admin.labels.Billing Days'))
                ->sortable(),
            TextColumn::make('status')
                ->label(__('admin.labels.Status'))
                ->searchable()
                ->badge()
                ->sortable(),
            TextColumn::make('amount')
                ->label(__('admin.labels.Amount'))
                ->sortable(),
            TextColumn::make('starts_at')
                ->label(__('admin.labels.Starts At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label(__('admin.labels.Ends At'))
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
