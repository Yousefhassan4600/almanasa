<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use App\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class AuditLogsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label(__('admin.labels.Date'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('user.phone')
                ->label(__('admin.labels.User'))
                ->formatStateUsing(fn (mixed $state, AuditLog $record): ?string => $record->user?->name ?: $state)
                ->searchable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable()
                ->sortable(),
            TextColumn::make('action')
                ->label(__('admin.labels.Action'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'created', 'restored' => 'success',
                    'updated' => 'warning',
                    'deleted', 'forceDeleted' => 'danger',
                    default => 'gray',
                })
                ->sortable(),
            TextColumn::make('auditable_type')
                ->label(__('admin.labels.Model'))
                ->formatStateUsing(fn (?string $state): ?string => $state ? class_basename($state) : null)
                ->searchable()
                ->sortable(),
            TextColumn::make('auditable_id')
                ->label(__('admin.labels.Record #'))
                ->sortable(),
            TextColumn::make('ip_address')
                ->label(__('admin.labels.IP Address'))
                ->searchable(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'created_at';
    }

    protected function extraFilters(): array
    {
        return [
            SelectFilter::make('action')
                ->options([
                    'created' => 'Created',
                    'updated' => 'Updated',
                    'deleted' => 'Deleted',
                    'forceDeleted' => 'Force Deleted',
                    'restored' => 'Restored',
                ]),
        ];
    }

    protected function hasEditAction(): bool
    {
        return false;
    }
}
