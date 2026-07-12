<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric(),
                TextInput::make('city_id')
                    ->label('City Id')
                    ->numeric(),
                TextInput::make('education_stage_id')
                    ->label('Education Stage Id')
                    ->numeric(),
                TextInput::make('grade_id')
                    ->label('Grade Id')
                    ->numeric(),
                TextInput::make('school_name')
                    ->label('School Name'),
            ]);
    }
}
