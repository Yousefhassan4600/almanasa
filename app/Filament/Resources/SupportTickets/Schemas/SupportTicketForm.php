<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('subject')
                    ->label('Subject')
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('status')
                    ->label('Status')
                    ->required(),
            ]);
    }
}
