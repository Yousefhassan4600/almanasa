<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('guard_name')
                    ->label('Guard Name')
                    ->default('web')
                    ->readOnly()
                    ->required(),
                Toggle::make('is_assignable')
                    ->label('Assignable')
                    ->default(true),
            ]);
    }
}
