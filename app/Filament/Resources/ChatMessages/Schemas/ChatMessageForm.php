<?php

namespace App\Filament\Resources\ChatMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChatMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('chat_room_id')
                    ->label('Chat Room Id')
                    ->numeric()
                    ->required(),
                TextInput::make('sender_user_id')
                    ->label('Sender User Id')
                    ->numeric()
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull(),
                TextInput::make('file_url')
                    ->label('File Url'),
                TextInput::make('file_name')
                    ->label('File Name'),
                TextInput::make('file_size')
                    ->label('File Size'),
            ]);
    }
}
