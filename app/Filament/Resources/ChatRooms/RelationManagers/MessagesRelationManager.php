<?php

namespace App\Filament\Resources\ChatRooms\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'messages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sender_user_id')
                    ->label(__('admin.labels.Sender'))
                    ->relationship('sender', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name ?: $record->phone)
                    ->preload()
                    ->searchable()
                    ->required(),
                Textarea::make('message')
                    ->label(__('admin.labels.Message'))
                    ->columnSpanFull(),
                TextInput::make('file_url')
                    ->label(__('admin.labels.File Url')),
                TextInput::make('file_name')
                    ->label(__('admin.labels.File Name')),
                TextInput::make('file_size')
                    ->label(__('admin.labels.File Size')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Messages')
            ->recordTitleAttribute('message')
            ->columns([
                TextColumn::make('sender.phone')
                    ->label(__('admin.labels.Sender'))
                    ->formatStateUsing(fn (mixed $state, mixed $record): ?string => $record->sender?->name ?: $state)
                    ->searchable(),
                TextColumn::make('message')
                    ->label(__('admin.labels.Message'))
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('file_name')
                    ->label(__('admin.labels.File Name'))
                    ->searchable(),
                TextColumn::make('file_size')
                    ->label(__('admin.labels.File Size')),
                TextColumn::make('created_at')
                    ->label(__('admin.labels.Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }
}
