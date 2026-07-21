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
use Illuminate\Database\Eloquent\Builder;

class MembersRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('admin.labels.User'))
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
                                $fail(__('admin.messages.user_already_chat_member'));
                            }
                        },
                    ])
                    ->required(),
                Select::make('role')
                    ->label(__('admin.labels.Role'))
                    ->options(ChatMemberRole::options())
                    ->required(),
                DateTimePicker::make('joined_at')
                    ->label(__('admin.labels.Joined At')),
                DateTimePicker::make('last_read_at')
                    ->label(__('admin.labels.Last Read At')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Members')
            ->recordTitleAttribute('user_id')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('user'))
            ->columns([
                TextColumn::make('user.phone')
                    ->label(__('admin.labels.User'))
                    ->formatStateUsing(fn (mixed $state, mixed $record): ?string => $record->user?->name ?: $state)
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('admin.labels.Role'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->label(__('admin.labels.Joined At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_read_at')
                    ->label(__('admin.labels.Last Read At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions($this->getTableHeaderActions())
            ->filters($this->getTableFilters())
            ->recordActions($this->getTableActions());
    }
}
