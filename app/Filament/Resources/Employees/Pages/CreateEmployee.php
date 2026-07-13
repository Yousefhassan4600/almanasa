<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Employees\EmployeeResource;

class CreateEmployee extends BaseCreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();

        return $data;
    }
}
