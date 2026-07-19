<?php

namespace App\Filament\Resources\ProviderCodes\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ProviderCodesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('code')
                ->label('Code')
                ->searchable()
                ->badge()
                ->sortable()
                ->copyable(),
            TextColumn::make('provider.name')
                ->label('Provider'),
            TextColumn::make('purchaseUnit.name')
                ->label('Purchase Unit')
                ->badge()
                ->searchable(),
            TextColumn::make('course.title')
                ->label('Course')
                ->searchable()
                ->toggleable(),
            TextColumn::make('lesson.title')
                ->label('Lesson')
                ->searchable()
                ->toggleable(),
            TextColumn::make('num_of_uses')
                ->label('Uses')
                ->badge()
                ->sortable(),
            TextColumn::make('expiry_date')
                ->label('Expiry Date')
                ->date()
                ->sortable(),
        ];
    }
}
