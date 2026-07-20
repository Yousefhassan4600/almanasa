<?php

namespace App\Filament\Resources\ChatRooms;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ChatRooms\Schemas\ChatRoomForm;
use App\Filament\Resources\ChatRooms\Tables\ChatRoomsTable;
use App\Models\ChatRoom;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ChatRoomResource extends BaseResource
{
    protected static ?string $model = ChatRoom::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ChatRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatRoomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatRooms::route('/'),
            'create' => Pages\CreateChatRoom::route('/create'),
            'edit' => Pages\EditChatRoom::route('/{record}/edit'),
        ];
    }
}
