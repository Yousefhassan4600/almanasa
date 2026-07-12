<?php

namespace App\Filament\Resources\ChatMembers;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ChatMembers\Schemas\ChatMemberForm;
use App\Filament\Resources\ChatMembers\Tables\ChatMembersTable;
use App\Models\ChatMember;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ChatMemberResource extends BaseResource
{
    protected static ?string $model = ChatMember::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return ChatMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatMembers::route('/'),
            'create' => Pages\CreateChatMember::route('/create'),
            'edit' => Pages\EditChatMember::route('/{record}/edit'),
        ];
    }
}
