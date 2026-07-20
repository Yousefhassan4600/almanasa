<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use App\Filament\Support\CurrentAccount;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CurrentAccount::providerTextInput(TextInput::make('provider_id'))
                    ->label(__('admin.labels.Provider Id'))
                    ->numeric(),
                TextInput::make('user_id')
                    ->label(__('admin.labels.User Id'))
                    ->numeric()
                    ->required(),
                TextInput::make('subject')
                    ->label(__('admin.labels.Subject'))
                    ->required(),
                Textarea::make('message')
                    ->label(__('admin.labels.Message'))
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('status')
                    ->label(__('admin.labels.Status'))
                    ->required(),
            ]);
    }
}
