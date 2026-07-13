<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class StudentProfilesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('user.phone')
                ->label('User Phone')
                ->searchable()
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),
            TextColumn::make('gender')
                ->label('Gender')
                ->searchable()
                ->sortable(),
            TextColumn::make('country.name')
                ->label('Country')
                ->searchable()
                ->sortable(),
            TextColumn::make('city.name')
                ->label('City')
                ->searchable()
                ->sortable(),
            TextColumn::make('education_stage.name')
                ->label('Education Stage')
                ->searchable()
                ->sortable(),
            TextColumn::make('grade.name')
                ->label('Grade')
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
