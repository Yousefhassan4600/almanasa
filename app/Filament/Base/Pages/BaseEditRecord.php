<?php

namespace App\Filament\Base\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

abstract class BaseEditRecord extends EditRecord
{
    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->hasDeleteAction()) {
            $actions[] = DeleteAction::make();
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

    protected function hasDeleteAction(): bool
    {
        return false;
    }
}
