<?php

namespace App\Filament\Resources\SupportTicketReplies;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\SupportTicketReplies\Schemas\SupportTicketReplyForm;
use App\Filament\Resources\SupportTicketReplies\Tables\SupportTicketRepliesTable;
use App\Models\SupportTicketReply;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketReplyResource extends BaseResource
{
    protected static ?string $model = SupportTicketReply::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return SupportTicketReplyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketRepliesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTicketReplies::route('/'),
            'create' => Pages\CreateSupportTicketReply::route('/create'),
            'edit' => Pages\EditSupportTicketReply::route('/{record}/edit'),
        ];
    }
}
