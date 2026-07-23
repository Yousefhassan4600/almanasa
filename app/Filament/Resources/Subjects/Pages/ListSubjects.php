<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Subjects\SubjectResource;
use Filament\Actions\CreateAction;

class ListSubjects extends BaseListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
