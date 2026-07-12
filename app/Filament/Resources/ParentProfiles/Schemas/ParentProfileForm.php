<?php

namespace App\Filament\Resources\ParentProfiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('occupation')
                    ->label('Occupation'),
            ]);
    }
}
