<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Employees\EmployeeResource;

class ListEmployees extends BaseListRecords
{
    protected static string $resource = EmployeeResource::class;
}
