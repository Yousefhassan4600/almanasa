<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\SupportTickets\Schemas\SupportTicketForm;
use App\Filament\Resources\SupportTickets\Tables\SupportTicketsTable;
use App\Models\SupportTicket;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketResource extends BaseResource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
