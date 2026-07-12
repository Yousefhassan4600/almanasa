<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('questionable_type')
                    ->label('Questionable Type')
                    ->required(),
                TextInput::make('questionable_id')
                    ->label('Questionable Id')
                    ->numeric()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(QuestionType::options())
                    ->required(),
                Textarea::make('title')
                    ->label('Title')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('points')
                    ->label('Points')
                    ->numeric()
                    ->required(),
            ]);
    }
}
