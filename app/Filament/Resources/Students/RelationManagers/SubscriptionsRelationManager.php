<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'subscriptions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course_id')
            ->modifyQueryUsing(fn (Builder $query): Builder => $this->scopeToCurrentTeacher($query)
                ->with(['provider', 'course', 'purchaseUnit']))
            ->columns([
                TextColumn::make('id')
                    ->label(__('admin.labels.#'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->label(__('admin.labels.Provider'))
                    ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                    ->searchable(),
                TextColumn::make('course.title')
                    ->label(__('admin.labels.Course')),
                TextColumn::make('purchaseUnit.name')
                    ->label(__('admin.labels.Purchase Unit'))
                    ->badge(),
                TextColumn::make('purchase_type')
                    ->label(__('admin.labels.Purchase Type'))
                    ->badge(),
                TextColumn::make('starts_at')
                    ->label(__('admin.labels.Starts At'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('admin.labels.Ends At'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('admin.labels.Is Active'))
                    ->boolean(),
            ])
            ->filters($this->getTableFilters())
            ->headerActions([])
            ->recordActions([]);
    }

    private function scopeToCurrentTeacher(Builder $query): Builder
    {
        $account = CurrentAccount::account();

        if (! $account || ! CurrentAccount::isAcademyTeacher()) {
            return $query;
        }

        return $query->whereHas('course.academyTeacher', fn (Builder $query): Builder => $query
            ->where('teacher_account_id', $account->id));
    }
}
