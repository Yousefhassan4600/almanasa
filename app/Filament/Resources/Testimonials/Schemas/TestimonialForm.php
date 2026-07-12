<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->label('User Id')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('role')
                    ->label('Role'),
                TextInput::make('rating')
                    ->label('Rating')
                    ->numeric(),
                Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
