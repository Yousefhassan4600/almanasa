<?php

namespace App\Filament\Resources\ChatMembers;

use App\Enums\ChatMemberRole;
use App\Filament\Resources\ChatMembers\Pages\ManageChatMembers;
use App\Models\ChatMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ChatMemberResource extends Resource
{
    protected static ?string $model = ChatMember::class;

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('chat_room_id')
                    ->label('Chat Room Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label('User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->label('Joined At')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_read_at')
                    ->label('Last Read At')
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
            'index' => ManageChatMembers::route('/'),
        ];
    }
}
