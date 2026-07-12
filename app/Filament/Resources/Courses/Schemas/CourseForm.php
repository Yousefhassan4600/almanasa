<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('teacher_account_id')
                    ->label('Teacher Account Id')
                    ->numeric(),
                Select::make('account_subject_id')
                    ->relationship('accountSubject', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('thumbnail')
                    ->label('Thumbnail'),
                TextInput::make('term')
                    ->label('Term'),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                TextInput::make('monthly_price')
                    ->label('Monthly Price')
                    ->numeric(),
                TextInput::make('weekly_lectures_count')
                    ->label('Weekly Lectures Count')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(ContentStatus::options())
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Is Featured'),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
            ]);
    }
}
