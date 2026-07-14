<?php

namespace App\Filament\Resources\AccountSubjects\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class AccountSubjectsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#'),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable(),
            TextColumn::make('gradeSubject.full_name')
                ->label('Grade Subject'),
            ToggleColumn::make('is_active')
                ->label('Is Active'),
        ];
    }

    protected function hasViewAction(): bool
    {
        return false;
    }
}
