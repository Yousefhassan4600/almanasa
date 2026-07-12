<?php

namespace App\Filament\Resources\AccountSubjects\Pages;

use App\Filament\Resources\AccountSubjects\AccountSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountSubjects extends ManageRecords
{
    protected static string $resource = AccountSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
