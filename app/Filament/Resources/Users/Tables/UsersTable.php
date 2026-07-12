<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class UsersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('first_name')
                ->label('First Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('last_name')
                ->label('Last Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),
            TextColumn::make('phone')
                ->label('Phone')
                ->searchable()
                ->sortable(),
            TextColumn::make('dial_country_code')
                ->label('Dial Country Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('gender')
                ->label('Gender')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
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
