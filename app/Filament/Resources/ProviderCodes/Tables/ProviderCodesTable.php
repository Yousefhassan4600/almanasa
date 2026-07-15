<?php

namespace App\Filament\Resources\ProviderCodes\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ProviderCodesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->label('Code')
                ->searchable()
                ->sortable()
                ->copyable(),
            TextColumn::make('purchaseUnit.name')
                ->label('Purchase Unit')
                ->searchable(),
            TextColumn::make('expiry_date')
                ->label('Expiry Date')
                ->date()
                ->sortable(),
            TextColumn::make('num_of_uses')
                ->label('Uses')
                ->sortable(),
        ];
    }
}
