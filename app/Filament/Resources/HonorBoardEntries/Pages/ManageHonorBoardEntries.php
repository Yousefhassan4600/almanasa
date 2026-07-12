<?php

namespace App\Filament\Resources\HonorBoardEntries\Pages;

use App\Filament\Resources\HonorBoardEntries\HonorBoardEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHonorBoardEntries extends ManageRecords
{
    protected static string $resource = HonorBoardEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
