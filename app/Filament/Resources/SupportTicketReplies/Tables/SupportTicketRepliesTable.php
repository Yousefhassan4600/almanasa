<?php

namespace App\Filament\Resources\SupportTicketReplies\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class SupportTicketRepliesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('support_ticket_id')
                ->label('Support Ticket Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('user_id')
                ->label('User Id')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
