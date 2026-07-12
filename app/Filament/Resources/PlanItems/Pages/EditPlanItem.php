<?php

namespace App\Filament\Resources\PlanItems\Pages;

use App\Filament\Resources\PlanItems\PlanItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanItem extends EditRecord
{
    protected static string $resource = PlanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
