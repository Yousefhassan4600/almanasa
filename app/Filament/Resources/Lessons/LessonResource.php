<?php

namespace App\Filament\Resources\Lessons;

use App\Enums\ContentStatus;
use App\Filament\Resources\Lessons\Pages\ManageLessons;
use App\Models\Lesson;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_unit_id')
                    ->label('Course Unit Id')
                    ->numeric(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('duration_seconds')
                    ->label('Duration Seconds')
                    ->numeric(),
                Toggle::make('is_free')
                    ->label('Is Free'),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(ContentStatus::options())
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_id')
                    ->label('Course Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_unit_id')
                    ->label('Course Unit Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_seconds')
                    ->label('Duration Seconds')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_free')
                    ->label('Is Free')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLessons::route('/'),
        ];
    }
}
