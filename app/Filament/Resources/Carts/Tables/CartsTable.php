<?php

namespace App\Filament\Resources\Carts\Tables;

use App\Filament\Base\BaseTable;
use App\Models\Cart;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

class CartsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student.phone')
                ->label('Student')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('purchase_type')
                ->label('Purchase Type')
                ->badge()
                ->searchable()
                ->sortable(),
            TextColumn::make('subtotal')
                ->label('Subtotal')
                ->searchable()
                ->sortable(),
            TextColumn::make('total')
                ->label('Total')
                ->searchable()
                ->sortable(),
            TextColumn::make('items_count')
                ->label('Items')
                ->counts('items')
                ->sortable(),
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
