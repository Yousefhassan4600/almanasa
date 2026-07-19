<?php

namespace App\Filament\Resources\Students\Pages;

use App\Enums\AccountType;
use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Students\StudentResource;

class CreateStudent extends BaseCreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = AccountType::Student->value;

        return $data;
    }
}
