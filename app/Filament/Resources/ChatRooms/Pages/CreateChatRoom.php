<?php

namespace App\Filament\Resources\ChatRooms\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\ChatRooms\ChatRoomResource;

class CreateChatRoom extends BaseCreateRecord
{
    protected static string $resource = ChatRoomResource::class;
}
