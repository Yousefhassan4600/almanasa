<?php

namespace App\Filament\Resources\ParentStudents\Pages;

use App\Filament\Resources\ParentStudents\ParentStudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageParentStudents extends ManageRecords
{
    protected static string $resource = ParentStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
