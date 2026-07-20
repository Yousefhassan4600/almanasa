<?php

namespace App\Filament\Resources\SupportTickets\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RepliesRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'replies';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name ?: $record->phone)
                    ->preload()
                    ->searchable()
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Replies')
            ->recordTitleAttribute('message')
            ->columns([
                TextColumn::make('user.phone')
                    ->label('User')
                    ->formatStateUsing(fn (mixed $state, mixed $record): ?string => $record->user?->name ?: $state)
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }
}
