<?php

namespace App\Filament\Resources\ChatMembers\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\ChatMembers\ChatMemberResource;

class ListChatMembers extends BaseListRecords
{
    protected static string $resource = ChatMemberResource::class;
}
