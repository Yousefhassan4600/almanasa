<?php

namespace App\Filament\Resources\AcademyTeachers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class AcademyTeachersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('teacher_account_id')
                ->label('Teacher Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            TextColumn::make('joined_at')
                ->label('Joined At')
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
