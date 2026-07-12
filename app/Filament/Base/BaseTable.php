<?php

namespace App\Filament\Base;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

abstract class BaseTable
{
    public static function configure(Table $table): Table
    {
        $instance = new static;
        $defaultSort = $instance->getDefaultSort();

        $configuredTable = $table
            ->defaultSort($defaultSort, $instance->getDefaultOrder())
            ->reorderable($defaultSort, $defaultSort !== 'id')
            ->columns($instance->columns())
            ->filters(array_merge(
                $instance->filters(),
                $instance->extraFilters()
            ), layout: FiltersLayout::AfterContentCollapsible)
            ->recordUrl($instance->getRecordUrl())
            ->recordActions($instance->recordActions())
            ->toolbarActions(array_merge(
                $instance->toolbarActions(),
                $instance->extraToolbarActions()
            ));

        return $configuredTable;
    }

    abstract protected function columns();

    protected function getDefaultSort(): ?string
    {
        return 'id';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'desc';
    }

    /**
     * Get the URL for clicking on a record row.
     * Return null to disable row clicking, or a closure to generate the URL.
     */
    protected function getRecordUrl(): ?\Closure
    {
        return null;
    }

    protected function filters(): array
    {
        return [
            // TrashedFilter::make(),
        ];
    }

    protected function extraFilters(): array
    {
        return [];
    }

    protected function recordActions(): array
    {
        $actions = [];

        if ($this->hasViewAction()) {
            $actions[] = ViewAction::make('view')
                ->hiddenLabel()
                ->color('info')
                ->tooltip(__('admin.View'))
                ->size('lg')
                ->icon(Heroicon::Eye);
        }

        if ($this->hasEditAction()) {
            $actions[] = EditAction::make()
                ->hiddenLabel()
                ->color('gray')
                ->size('lg')
                ->color('primary')
                ->tooltip(__('admin.Edit'))
                ->icon(Heroicon::PencilSquare)
                ->hidden(fn ($record) => method_exists($record, 'trashed') && $record->trashed());
        }

        // if ($this->hasRestoreAction()) {
        //     $actions[] = RestoreAction::make()
        //         ->hiddenLabel()
        //         ->tooltip(__('admin.Restore'))
        //         ->size('lg')
        //         ->color('danger')
        //         ->icon(Heroicon::ArrowPath)
        //         ->visible(fn($record) => method_exists($record, 'trashed') && $record->trashed());
        // }

        return array_merge(
            $actions,
            $this->extraRecordActions()
        );
    }

    protected function hasViewAction(): bool
    {
        return true;
    }

    protected function hasEditAction(): bool
    {
        return true;
    }

    protected function hasRestoreAction(): bool
    {
        return false;
    }

    protected function extraRecordActions(): array
    {
        return [];
    }

    protected function toolbarActions(): array
    {
        return [
            BulkActionGroup::make([]),
        ];
    }

    protected function extraToolbarActions(): array
    {
        return [];
    }
}
