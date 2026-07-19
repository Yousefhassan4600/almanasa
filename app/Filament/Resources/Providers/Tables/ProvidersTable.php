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

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('owner.name')
                ->label('Owner User')
                ->searchable()
                ->sortable(),
            TextColumn::make('currentSubscription.status')
                ->label('Subscription')
                ->badge()
                ->searchable(),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean(),
            IconColumn::make('pause_website')
                ->label('Website Paused')
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
