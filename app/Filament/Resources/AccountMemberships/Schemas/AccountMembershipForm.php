<?php

namespace App\Filament\Resources\AccountMemberships\Schemas;

use App\Enums\AccountMemberRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccountMembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                Select::make('predefined_role')
                    ->label('Predefined Role')
                    ->options(AccountMemberRole::options())
                    ->required(),
                TextInput::make('role_id')
                    ->label('Custom Role Id')
                    ->numeric(),
                TextInput::make('created_by_user_id')
                    ->label('Created By User Id')
                    ->numeric(),
                TextInput::make('status')
                    ->label('Status')
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
            ]);
    }
}
