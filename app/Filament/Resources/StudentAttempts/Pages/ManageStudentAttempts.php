<?php

namespace App\Filament\Resources\StudentAttempts\Pages;

use App\Filament\Resources\StudentAttempts\StudentAttemptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentAttempts extends ManageRecords
{
    protected static string $resource = StudentAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
