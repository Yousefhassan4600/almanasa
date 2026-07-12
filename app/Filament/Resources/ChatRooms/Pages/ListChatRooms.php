<?php

namespace App\Filament\Resources\ChatRooms\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\ChatRooms\ChatRoomResource;

class ListChatRooms extends BaseListRecords
{
    protected static string $resource = ChatRoomResource::class;
}
