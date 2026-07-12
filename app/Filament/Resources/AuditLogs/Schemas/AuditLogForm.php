<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric(),
                TextInput::make('action')
                    ->label('Action')
                    ->required(),
                TextInput::make('auditable_type')
                    ->label('Auditable Type'),
                TextInput::make('auditable_id')
                    ->label('Auditable Id')
                    ->numeric(),
                Textarea::make('old_values')
                    ->label('Old Values')
                    ->columnSpanFull(),
                Textarea::make('new_values')
                    ->label('New Values')
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('Ip Address'),
                Textarea::make('user_agent')
                    ->label('User Agent')
                    ->columnSpanFull(),
            ]);
    }
}
