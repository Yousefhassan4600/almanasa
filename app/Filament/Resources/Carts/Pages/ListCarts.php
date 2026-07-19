<?php

namespace App\Filament\Resources\Carts\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Carts\CartResource;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCarts extends BaseListRecords
{
    protected static string $resource = CartResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),
            'unactive' => Tab::make('Unactive')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
