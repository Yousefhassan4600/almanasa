<?php

namespace App\Filament\Resources\TenantGradeSubjects\Pages;

use App\Filament\Resources\TenantGradeSubjects\TenantGradeSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenantGradeSubjects extends ListRecords
{
    protected static string $resource = TenantGradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
