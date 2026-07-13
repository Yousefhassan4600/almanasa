<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->preload()
                    ->searchable(),
                Select::make('grade_id')
                    ->label('Grade')
                    ->relationship('grade', 'name')
                    ->preload()
                    ->searchable(),
                TextInput::make('school_name')
                    ->label('School Name'),
            ]);
    }
}
