<?php

namespace App\Filament\Resources\PaymentCodes\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PaymentCodesTable extends BaseTable
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
            TextColumn::make('amount')
                ->label('Amount')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_days')
                ->label('Duration Days')
                ->searchable()
                ->sortable(),
            TextColumn::make('max_uses')
                ->label('Max Uses')
                ->searchable()
                ->sortable(),
            TextColumn::make('used_count')
                ->label('Used Count')
                ->searchable()
                ->sortable(),
            TextColumn::make('expires_at')
                ->label('Expires At')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
