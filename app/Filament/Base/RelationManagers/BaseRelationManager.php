<?php

namespace App\Filament\Base\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\TrashedFilter;

abstract class BaseRelationManager extends RelationManager
{
    public function getTableHeaderActions(): array
    {
        return array_merge(
            [
                CreateAction::make()
                    ->color('primary'),
            ],
            $this->extraHeaderActions()
        );
    }

    protected function extraHeaderActions(): array
    {
        return [];
    }

    public function getTableFilters(): array
    {
        return array_merge(
            [
                TrashedFilter::make(),
            ],
            $this->extraTableFilters()
        );
    }

    protected function extraTableFilters(): array
    {
        return [];
    }

    public function getTableActions(): array
    {
        return array_merge(
            [
                ViewAction::make('view')
                    ->hiddenLabel()
                    ->color('info')
                    ->tooltip(__('admin.View'))
                    ->size('lg')
                    ->icon(Heroicon::Eye),
                EditAction::make()
                    ->hiddenLabel()
                    ->size('lg')
                    ->color('primary')
                    ->tooltip(__('admin.Edit'))
                    ->icon(Heroicon::PencilSquare)
                    ->hidden(fn ($record) => method_exists($record, 'trashed') && $record->trashed()),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('admin.Delete'))
                    ->size('lg')
                    ->icon(Heroicon::Trash)
                    ->hidden(fn ($record) => method_exists($record, 'trashed') && $record->trashed()),
                RestoreAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('admin.Restore'))
                    ->size('lg')
                    ->color('danger')
                    ->icon(Heroicon::ArrowPath)
                    ->visible(fn ($record) => method_exists($record, 'trashed') && $record->trashed()),

            ],
            $this->extraTableActions()
        );
    }

    protected function extraTableActions(): array
    {
        return [];
    }
}
