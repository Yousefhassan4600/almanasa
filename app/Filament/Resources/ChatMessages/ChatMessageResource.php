<?php

namespace App\Filament\Resources\ChatMessages;

use App\Filament\Resources\ChatMessages\Pages\ManageChatMessages;
use App\Models\ChatMessage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ChatMessageResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chat_room_id')
                    ->label('Chat Room Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sender_user_id')
                    ->label('Sender User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_url')
                    ->label('File Url')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_size')
                    ->label('File Size')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageChatMessages::route('/'),
        ];
    }
}
