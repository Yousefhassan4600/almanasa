<?php

namespace App\Filament\Resources\ChatMessages\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\ChatMessages\ChatMessageResource;

class EditChatMessage extends BaseEditRecord
{
    protected static string $resource = ChatMessageResource::class;
}
