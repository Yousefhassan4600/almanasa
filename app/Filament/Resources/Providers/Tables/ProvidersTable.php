<?php

namespace App\Filament\Resources\Providers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvidersTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table);
    }

    protected function eagerLoads(): array
    {
        return [
            'owner',
            'currentSubscription',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label(__('admin.labels.Name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label(__('admin.labels.Type'))
                ->searchable()
                ->sortable(),
            TextColumn::make('owner.name')
                ->label(__('admin.labels.Owner User'))
                ->searchable()
                ->sortable(),
            TextColumn::make('currentSubscription.status')
                ->label(__('admin.labels.Subscription'))
                ->badge()
                ->searchable(),
            IconColumn::make('is_active')
                ->label(__('admin.labels.Is Active'))
                ->boolean(),
            IconColumn::make('pause_website')
                ->label(__('admin.labels.Website Paused'))
                ->boolean(),
        ];
    }

    protected function extraRecordActions(): array
    {
        return [
            Action::make('open_website')
                ->label('')
                ->url(fn ($record): string => $record->website_url)
                ->openUrlInNewTab()
                ->icon('heroicon-o-link'),
        ];
    }
}
