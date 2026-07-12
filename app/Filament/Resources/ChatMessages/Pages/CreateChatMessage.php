<?php

namespace App\Filament\Resources\ChatMessages\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\ChatMessages\ChatMessageResource;

class CreateChatMessage extends BaseCreateRecord
{
    protected static string $resource = ChatMessageResource::class;
}
