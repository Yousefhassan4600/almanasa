<?php

namespace App\Filament\Resources\TenantGradeSubjects\Pages;

use App\Filament\Resources\TenantGradeSubjects\TenantGradeSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantGradeSubject extends EditRecord
{
    protected static string $resource = TenantGradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
