<?php

namespace App\Filament\Resources\ChatMessages\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\ChatMessages\ChatMessageResource;

class ListChatMessages extends BaseListRecords
{
    protected static string $resource = ChatMessageResource::class;
}
