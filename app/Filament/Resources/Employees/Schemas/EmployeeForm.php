<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\EmployeeRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('Account')
                    ->relationship('account', 'id')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'phone')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('predefined_role')
                    ->label('Predefined Role')
                    ->options(EmployeeRole::options())
                    ->required(),
                Select::make('role_id')
                    ->label('Custom Role')
                    ->relationship('role', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('created_by_user_id')
                    ->label('Created By')
                    ->relationship('creator', 'phone')
                    ->preload()
                    ->searchable(),
                TextInput::make('status')
                    ->label('Status')
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
            ]);
    }
}
