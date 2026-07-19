<?php

namespace App\Filament\Resources\PaymentMethods\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class PaymentMethodsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            ImageColumn::make('image')
                ->label('Image'),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->wrap(),
            TextColumn::make('slug')
                ->label('Slug')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_bank')
                ->label('Is Bank')
                ->boolean(),
            IconColumn::make('is_code')
                ->label('Is Code')
                ->boolean(),
            IconColumn::make('require_proof')
                ->label('Require Proof')
                ->boolean(),
            ToggleColumn::make('is_active')
                ->label('Is Active'),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }

    public function hasViewAction(): bool
    {
        return false;
    }

    public function hasEditAction(): bool
    {
        return false;
    }
}
