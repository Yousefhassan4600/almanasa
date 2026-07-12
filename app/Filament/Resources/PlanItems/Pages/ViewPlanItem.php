<?php

namespace App\Filament\Resources\PlanItems\Pages;

use App\Filament\Resources\PlanItems\PlanItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlanItem extends ViewRecord
{
    protected static string $resource = PlanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
