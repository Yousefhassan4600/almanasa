<?php

namespace App\Filament\Resources\LessonItems;

use App\Enums\LessonItemType;
use App\Filament\Resources\LessonItems\Pages\ManageLessonItems;
use App\Models\LessonItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LessonItemResource extends Resource
{
    protected static ?string $model = LessonItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lesson_id')
                    ->label('Lesson Id')
                    ->numeric()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options(LessonItemType::options())
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('video_url')
                    ->label('Video Url'),
                TextInput::make('file_url')
                    ->label('File Url'),
                TextInput::make('duration_seconds')
                    ->label('Duration Seconds')
                    ->numeric(),
                TextInput::make('assignment_id')
                    ->label('Assignment Id')
                    ->numeric(),
                TextInput::make('exam_id')
                    ->label('Exam Id')
                    ->numeric(),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required(),
                Toggle::make('is_required')
                    ->label('Is Required'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson_id')
                    ->label('Lesson Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('video_url')
                    ->label('Video Url')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_url')
                    ->label('File Url')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_seconds')
                    ->label('Duration Seconds')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignment_id')
                    ->label('Assignment Id')
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
            'index' => ManageLessonItems::route('/'),
        ];
    }
}
