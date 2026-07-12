<?php

namespace App\Filament\Resources\SupportTicketReplies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketReplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('support_ticket_id')
                    ->label('Support Ticket Id')
                    ->numeric()
                    ->required(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
