<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Employees\EmployeeResource;

class CreateEmployee extends BaseCreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
