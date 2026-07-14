<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class StudentProfilesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            ImageColumn::make('avatar')
                ->label('Avatar')
                ->circular(),
            TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            TextColumn::make('educationStage.name')
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

        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
