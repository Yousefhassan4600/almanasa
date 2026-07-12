<?php

namespace App\Filament\Resources\PackageCourses\Pages;

use App\Filament\Resources\PackageCourses\PackageCourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePackageCourses extends ManageRecords
{
    protected static string $resource = PackageCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
