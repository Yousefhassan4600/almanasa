<?php

namespace App\Filament\Resources\Carts\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use App\Models\Cart;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

class CartsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'provider',
            'student',
            'items.course',
            'items.purchaseUnit',
            'purchaseUnit',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->searchable()
                ->sortable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner()),
            TextColumn::make('student.name')
                ->label(__('admin.labels.Student'))
                ->searchable(),
            TextColumn::make('items')
                ->label(__('admin.labels.Courses'))
                ->state(function (Cart $record): array {
                    $record->loadMissing('items.course');

                    return $record->items
                        ->pluck('course.title')
                        ->filter()
                        ->values()
                        ->all();
                })
                ->listWithLineBreaks()
                ->badge()
                ->color('info'),
            TextColumn::make('purchaseUnit.name')
                ->label(__('admin.labels.Purchase Unit'))
                ->badge()
                ->placeholder('-'),
            TextColumn::make('purchase_type')
                ->label(__('admin.labels.Purchase Type'))
                ->badge(),
            TextColumn::make('total')
                ->label(__('admin.labels.Total'))
                ->suffix(' EGP')
                ->badge()
                ->color('success'),
        ];
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return false;
    }

    protected function extraRecordActions(): array
    {
        return [
            Action::make('items')
                ->label('')
                ->icon('heroicon-o-shopping-bag')
                ->color('info')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading(fn (Cart $record): string => 'Cart Items #'.$record->getKey())
                ->modalContent(fn (Cart $record): View => view('filament.resources.carts.items-modal', [
                    'items' => $record->items()
                        ->with(['course', 'purchaseUnit'])
                        ->latest()
                        ->get(),
                ])),
        ];
    }
}
