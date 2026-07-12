<?php

namespace App\Filament\Resources\SupportTicketReplies\Pages;

use App\Filament\Resources\SupportTicketReplies\SupportTicketReplyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSupportTicketReplies extends ManageRecords
{
    protected static string $resource = SupportTicketReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
