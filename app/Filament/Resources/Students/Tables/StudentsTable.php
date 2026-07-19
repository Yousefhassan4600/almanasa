<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Base\BaseTable;
use App\Models\Account;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class StudentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider'),
            ImageColumn::make('owner.studentProfile.avatar')
                ->label('Image'),
            TextColumn::make('owner.name')
                ->label('Student')
                ->searchable(),
            TextColumn::make('owner.phone')
                ->label('Phone')
                ->searchable(),
            TextColumn::make('owner.studentProfile.country_id')
                ->label('Location')
                ->formatStateUsing(function ($state, Account $record) {
                    $country = $record->owner?->studentProfile?->country?->name;
                    $city = $record->owner?->studentProfile?->city?->name;

                    if ($country && $city) {
                        return "{$city}, {$country}";
                    } elseif ($country) {
                        return $country;
                    } elseif ($city) {
                        return $city;
                    }

                    return '-';
                }),
            TextColumn::make('owner.studentProfile.grade.full_name')
                ->label('Grade')
                ->badge()
                ->color('info')
                ->placeholder('-'),
            TextColumn::make('owner.studentProfile.gender')
                ->label('Gender')
                ->badge(),
            TextColumn::make('owner.studentProfile.school_name')
                ->label('School')
                ->wrap(),
            IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Joined At')
                ->dateTime()
                ->sortable(),
        ];
    }
}
