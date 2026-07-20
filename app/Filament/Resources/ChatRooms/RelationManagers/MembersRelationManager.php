<?php

namespace App\Filament\Resources\ChatRooms\RelationManagers;

use App\Enums\ChatMemberRole;
use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Models\ChatMember;
use App\Models\User;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'members';

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
                    ->rules([
                        fn (?ChatMember $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                            if (blank($value)) {
                                return;
                            }

                            $memberExists = ChatMember::query()
                                ->where('chat_room_id', $this->getOwnerRecord()->getKey())
                                ->where('user_id', $value)
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($memberExists) {
                                $fail('This user is already a member of this chat room.');
                            }
                        },
                    ])
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->options(ChatMemberRole::options())
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label('Joined At'),
                DateTimePicker::make('last_read_at')
                    ->label('Last Read At'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Members')
            ->recordTitleAttribute('user_id')
            ->columns([
                TextColumn::make('user.phone')
                    ->label('User')
                    ->formatStateUsing(fn (mixed $state, mixed $record): ?string => $record->user?->name ?: $state)
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->label('Joined At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_read_at')
                    ->label('Last Read At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }
}
