<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Employees\EmployeeResource;

class EditEmployee extends BaseEditRecord
{
    protected static string $resource = EmployeeResource::class;
}
