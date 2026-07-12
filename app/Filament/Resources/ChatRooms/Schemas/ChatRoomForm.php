<?php

namespace App\Filament\Resources\ChatRooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChatRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric(),
                TextInput::make('lesson_id')
                    ->label('Lesson Id')
                    ->numeric(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
