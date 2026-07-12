<?php

namespace App\Filament\Base\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

abstract class BaseListRecords extends ListRecords
{
    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->hasCreateAction()) {
            $actions[] = CreateAction::make();
        }

        return array_merge(
            $actions,
            $this->extraRecordActions()
        );
    }

    protected function extraRecordActions(): array
    {
        return [];
    }

    protected function hasCreateAction(): bool
    {
        return true;
    }
}
