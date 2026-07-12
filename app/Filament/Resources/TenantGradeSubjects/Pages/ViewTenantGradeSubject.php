<?php

namespace App\Filament\Resources\TenantGradeSubjects\Pages;

use App\Filament\Resources\TenantGradeSubjects\TenantGradeSubjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTenantGradeSubject extends ViewRecord
{
    protected static string $resource = TenantGradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
