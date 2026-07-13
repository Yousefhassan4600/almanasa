<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'phone')
                    ->preload()
                    ->searchable()
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                TextInput::make('avatar')
                    ->label('Avatar'),
                Select::make('gender')
                    ->label('Gender')
                    ->options(Gender::options()),
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('education_stage_id')
                    ->label('Education Stage')
                    ->relationship('education_stage', 'name')
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('grade_id', null))
                    ->preload()
                    ->searchable(),
                Select::make('grade_id')
                    ->label('Grade')
                    ->relationship(
                        name: 'grade',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->when(
                                filled($get('education_stage_id')),
                                fn (Builder $query): Builder => $query->where('education_stage_id', $get('education_stage_id')),
                                fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
                            )
                    )
                    ->disabled(fn (Get $get): bool => blank($get('education_stage_id')))
                    ->preload()
                    ->searchable(),
                TextInput::make('school_name')
                    ->label('School Name'),
            ]);
    }
}
