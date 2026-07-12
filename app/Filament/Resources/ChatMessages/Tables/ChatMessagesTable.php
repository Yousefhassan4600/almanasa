<?php

namespace App\Filament\Resources\ChatMessages\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ChatMessagesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
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
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
