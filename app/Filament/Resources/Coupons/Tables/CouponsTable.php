<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CouponsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->label('Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('discount_type')
                ->label('Discount Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('value')
                ->label('Value')
                ->searchable()
                ->sortable(),
            TextColumn::make('usage_limit')
                ->label('Usage Limit')
                ->searchable()
                ->sortable(),
            TextColumn::make('usage_limit_per_user')
                ->label('Usage Limit Per User')
                ->searchable()
                ->sortable(),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->searchable()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label('Ends At')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
