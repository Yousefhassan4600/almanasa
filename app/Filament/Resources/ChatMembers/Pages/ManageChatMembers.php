<?php

namespace App\Filament\Resources\ChatMembers\Pages;

use App\Filament\Resources\ChatMembers\ChatMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageChatMembers extends ManageRecords
{
    protected static string $resource = ChatMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
