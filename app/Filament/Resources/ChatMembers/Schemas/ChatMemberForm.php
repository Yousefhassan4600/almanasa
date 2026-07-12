<?php

namespace App\Filament\Resources\ChatMembers\Schemas;

use App\Enums\ChatMemberRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChatMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('chat_room_id')
                    ->label('Chat Room Id')
                    ->numeric()
                    ->required(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric()
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->options(ChatMemberRole::options())
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
                DateTimePicker::make('last_read_at')
                    ->label('Last Read At'),
            ]);
    }
}
