<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use App\Models\AuditLog;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Action Details')
                    ->schema([
                        TextEntry::make('action')
                            ->label('Action')
                            ->badge(),
                        TextEntry::make('user.phone')
                            ->label('User')
                            ->formatStateUsing(fn (mixed $state, AuditLog $record): ?string => $record->user?->name ?: $state),
                        TextEntry::make('provider.name')
                            ->label('Provider'),
                        TextEntry::make('auditable_label')
                            ->label('Record')
                            ->state(fn (AuditLog $record): string => class_basename((string) $record->auditable_type).' #'.$record->auditable_id),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(3),
                Section::make('Changed Data')
                    ->schema([
                        TextEntry::make('old_values')
                            ->label('Old Values')
                            ->formatStateUsing(fn (mixed $state, AuditLog $record): View => static::valuesView($record->old_values ?? [], $record))
                            ->columnSpanFull(),
                        TextEntry::make('new_values')
                            ->label('New Values')
                            ->formatStateUsing(fn (mixed $state, AuditLog $record): View => static::valuesView($record->new_values ?? [], $record))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private static function valuesView(array $values, AuditLog $record): View
    {
        return view('filament.infolists.audit-log-values', [
            'values' => $values,
            'changedKeys' => static::changedKeys($record),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private static function changedKeys(AuditLog $record): array
    {
        return array_values(array_unique([
            ...array_keys($record->old_values ?? []),
            ...array_keys($record->new_values ?? []),
        ]));
    }
}
