<?php

namespace App\Filament\Resources\ChatMembers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ChatMembersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
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
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
