<?php

namespace App\Filament\Resources\ParentProfiles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ParentProfilesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('user_id')
                ->label('User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('occupation')
                ->label('Occupation')
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
