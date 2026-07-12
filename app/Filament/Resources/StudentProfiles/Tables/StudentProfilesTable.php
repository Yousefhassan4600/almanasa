<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class StudentProfilesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('user_id')
                ->label('User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('country_id')
                ->label('Country Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('city_id')
                ->label('City Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('education_stage_id')
                ->label('Education Stage Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('grade_id')
                ->label('Grade Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('school_name')
                ->label('School Name')
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
