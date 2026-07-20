<?php

namespace App\Filament\Resources\Notifications\Schemas;

use App\Filament\Support\CurrentAccount;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->label(__('admin.labels.User Id'))
                    ->numeric()
                    ->required(),
                CurrentAccount::providerTextInput(TextInput::make('provider_id'))
                    ->label(__('admin.labels.Provider Id'))
                    ->numeric(),
                TextInput::make('title')
                    ->label(__('admin.labels.Title'))
                    ->required(),
                Textarea::make('body')
                    ->label(__('admin.labels.Body'))
                    ->columnSpanFull(),
                Textarea::make('data')
                    ->label(__('admin.labels.Data'))
                    ->columnSpanFull(),
                DateTimePicker::make('read_at')
                    ->label(__('admin.labels.Read At')),
            ]);
    }
}
