<?php

namespace App\Filament\Resources\ChatMembers\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\ChatMembers\ChatMemberResource;

class CreateChatMember extends BaseCreateRecord
{
    protected static string $resource = ChatMemberResource::class;
}
