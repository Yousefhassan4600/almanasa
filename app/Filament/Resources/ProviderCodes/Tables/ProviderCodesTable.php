<?php

namespace App\Filament\Resources\ProviderCodes\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;

class ProviderCodesTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'provider',
            'purchaseUnit',
            'course',
            'lesson',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('code')
                ->label(__('admin.labels.Code'))
                ->searchable()
                ->badge()
                ->sortable()
                ->copyable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner()),
            TextColumn::make('purchaseUnit.name')
                ->label(__('admin.labels.Purchase Unit'))
                ->badge()
                ->searchable(),
            TextColumn::make('course.title')
                ->label(__('admin.labels.Course'))
                ->searchable()
                ->toggleable(),
            TextColumn::make('lesson.title')
                ->label(__('admin.labels.Lesson'))
                ->searchable()
                ->toggleable(),
            TextColumn::make('num_of_uses')
                ->label(__('admin.labels.Uses'))
                ->badge()
                ->sortable(),
            TextColumn::make('expiry_date')
                ->label(__('admin.labels.Expiry Date'))
                ->date()
                ->sortable(),
        ];
    }
}
