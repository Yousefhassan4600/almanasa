<?php

namespace App\Filament\Resources\LessonContents;

use App\Enums\LessonContentType;
use App\Filament\Resources\LessonContents\Pages\CreateLessonContent;
use App\Filament\Resources\LessonContents\Pages\EditLessonContent;
use App\Filament\Resources\LessonContents\Pages\ListLessonContents;
use App\Filament\Resources\LessonContents\Pages\ViewLessonContent;
use App\Models\LessonContent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LessonContentResource extends Resource
{
    protected static ?string $model = LessonContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Course Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->required(),
                Select::make('type')
                    ->options(LessonContentType::options())
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_required')
                    ->required(),
                Toggle::make('is_preview')
                    ->required(),
                DateTimePicker::make('available_at'),
                TextInput::make('contentable_type'),
                TextInput::make('contentable_id')
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('lesson.title')
                    ->label('Lesson'),
                TextEntry::make('type'),
                TextEntry::make('title'),
                TextEntry::make('sort_order')
                    ->numeric(),
                IconEntry::make('is_required')
                    ->boolean(),
                IconEntry::make('is_preview')
                    ->boolean(),
                TextEntry::make('available_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('contentable_type')
                    ->placeholder('-'),
                TextEntry::make('contentable_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson.title')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_required')
                    ->boolean(),
                IconColumn::make('is_preview')
                    ->boolean(),
                TextColumn::make('available_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('contentable_type')
                    ->searchable(),
                TextColumn::make('contentable_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLessonContents::route('/'),
            'create' => CreateLessonContent::route('/create'),
            'view' => ViewLessonContent::route('/{record}'),
            'edit' => EditLessonContent::route('/{record}/edit'),
        ];
    }
}
