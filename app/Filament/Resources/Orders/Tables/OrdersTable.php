<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Base\BaseTable;
use App\Models\Order;
use App\Models\OrderStatusType;
use Filament\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Contracts\View\View;

class OrdersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('order_number')
                ->label('#')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('student.name')
                ->label('Student')
                ->searchable(),
            TextColumn::make('items')
                ->label('Courses')
                ->state(function (Order $record): array {
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
                ->label('Purchase Unit')
                ->badge()
                ->placeholder('-'),
            TextColumn::make('purchase_type')
                ->label('Purchase Type')
                ->badge(),
            TextColumn::make('total')
                ->label('Total')
                ->suffix(' EGP')
                ->badge()
                ->color('success'),
            SelectColumn::make('current_status_type_id')
                ->label('Status')
                ->getStateUsing(fn (Order $record): ?int => $record->currentStatus?->order_status_type_id)
                ->options(fn (): array => OrderStatusType::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(fn (OrderStatusType $statusType): array => [$statusType->id => $statusType->name])
                    ->all())
                ->selectablePlaceholder(false)
                ->updateStateUsing(function (Order $record, mixed $state): mixed {
                    $this->updateCurrentStatus($record, (int) $state);

                    return $state;
                }),
            ToggleColumn::make('is_paid')
                ->label('Paid')
                ->getStateUsing(fn (Order $record): bool => (bool) $record->payments()
                    ->latest()
                    ->value('is_paid'))
                ->updateStateUsing(function (Order $record, mixed $state): bool {
                    $payment = $record->payments()
                        ->latest()
                        ->first();

                    if ($payment) {
                        $payment->update([
                            'is_paid' => (bool) $state,
                        ]);
                    }

                    return (bool) $state;
                }),
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
                        ->with(['providerPaymentMethod.paymentMethod', 'providerCode'])
                        ->latest()
                        ->get(),
                ])),
            Action::make('logs')
                ->label('')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading('Status History')
                ->modalContent(fn (Order $record): View => view('filament.resources.orders.status-logs-modal', [
                    'statusLogs' => $record->statuses()
                        ->with(['type', 'createdBy'])
                        ->latest('status_at')
                        ->latest()
                        ->get(),
                ])),
        ];
    }

    private function updateCurrentStatus(Order $record, int $statusTypeId): void
    {
        if ($statusTypeId <= 0 || $record->currentStatus?->order_status_type_id === $statusTypeId) {
            return;
        }

        $record->statuses()->create([
            'order_status_type_id' => $statusTypeId,
            'created_by_user_id' => auth()->id(),
            'is_current' => true,
            'status_at' => now(),
        ]);
    }
}
