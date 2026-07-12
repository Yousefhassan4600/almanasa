<?php

namespace App\Filament\Resources\StudentAcademicProfiles\Pages;

use App\Filament\Resources\StudentAcademicProfiles\StudentAcademicProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentAcademicProfile extends EditRecord
{
    protected static string $resource = StudentAcademicProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
