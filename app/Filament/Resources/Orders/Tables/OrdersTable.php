<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Base\BaseTable;
use App\Models\Order;
use App\Models\OrderStatusType;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

class OrdersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('student.phone')
                ->label('Student')
                ->searchable()
                ->sortable(),
            TextColumn::make('order_number')
                ->label('Order Number')
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
            TextColumn::make('currentStatus.type.name')
                ->label('Status')
                ->badge()
                ->color(fn (Order $record): string => $this->statusBadgeColor($record))
                ->placeholder('-'),
            TextColumn::make('items_count')
                ->label('Items')
                ->counts('items')
                ->sortable(),
            TextColumn::make('payments_count')
                ->label('Payments')
                ->counts('payments')
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
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
                ->modalHeading(fn (Order $record): string => 'Order Items - '.$record->order_number)
                ->modalContent(fn (Order $record): View => view('filament.resources.orders.items-modal', [
                    'items' => $record->items()
                        ->with(['course', 'purchaseUnit'])
                        ->latest()
                        ->get(),
                ])),
            Action::make('payments')
                ->label('')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading(fn (Order $record): string => 'Payments - '.$record->order_number)
                ->modalContent(fn (Order $record): View => view('filament.resources.orders.payments-modal', [
                    'payments' => $record->payments()
                        ->with(['providerPaymentMethod.paymentMethod', 'providerCode', 'reviewedBy'])
                        ->latest()
                        ->get(),
                ])),
        ];
    }

    private function statusBadgeColor(Order $record): string
    {
        $sortOrder = $record->currentStatus?->type?->sort_order;

        if ($sortOrder === null) {
            return 'gray';
        }

        [$firstSortOrder, $lastSortOrder] = $this->statusTypeSortBounds();

        return match ($sortOrder) {
            $firstSortOrder => 'warning',
            $lastSortOrder => 'success',
            default => 'info',
        };
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function statusTypeSortBounds(): array
    {
        static $bounds = null;

        if ($bounds !== null) {
            return $bounds;
        }

        $sortOrders = OrderStatusType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('sort_order');

        return $bounds = [
            $sortOrders->first(),
            $sortOrders->last(),
        ];
    }
}
