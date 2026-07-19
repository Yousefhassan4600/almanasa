<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\Account;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'subscriptions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course_id')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable(),
                TextColumn::make('course.title')
                    ->label('Course'),
                TextColumn::make('purchaseUnit.name')
                    ->label('Purchase Unit')
                    ->searchable(),
                TextColumn::make('purchase_type')
                    ->label('Purchase Type')
                    ->badge(),
                TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Ends At')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Is Active')
                    ->boolean(),
            ])
            ->filters($this->getTableFilters())
            ->headerActions([])
            ->recordActions([]);
    }

    private function studentAccount(): Account
    {
        /** @var Account $account */
        $account = $this->getOwnerRecord();

        return $account;
    }
}
