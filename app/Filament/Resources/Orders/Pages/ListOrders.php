<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderStatusType;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends BaseListRecords
{
    protected static string $resource = OrderResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make(__('admin.labels.All')),
        ];

        OrderStatusType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (OrderStatusType $statusType) use (&$tabs): void {
                $tabs[$statusType->slug] = Tab::make($statusType->name)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas(
                        'statuses',
                        fn (Builder $query): Builder => $query
                            ->where('order_status_type_id', $statusType->id)
                            ->where('is_current', true)
                    ));
            });

        return $tabs;
    }
}
