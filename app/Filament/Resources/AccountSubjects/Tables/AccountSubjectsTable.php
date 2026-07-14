<?php

namespace App\Filament\Resources\AccountSubjects\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

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

    protected function hasViewAction(): bool
    {
        return false;
    }
}
