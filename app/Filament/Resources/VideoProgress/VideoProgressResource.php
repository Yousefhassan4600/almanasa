<?php

namespace App\Filament\Resources\VideoProgress;

use App\Enums\ProgressStatus;
use App\Filament\Resources\VideoProgress\Pages\CreateVideoProgress;
use App\Filament\Resources\VideoProgress\Pages\EditVideoProgress;
use App\Filament\Resources\VideoProgress\Pages\ListVideoProgress;
use App\Filament\Resources\VideoProgress\Pages\ViewVideoProgress;
use App\Models\VideoProgress;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VideoProgressResource extends Resource
{
    protected static ?string $model = VideoProgress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Progress';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                Select::make('video_id')
                    ->relationship('video', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Video')
                    ->searchable()
                    ->preload(),
                Select::make('lesson_content_id')
                    ->relationship('lessonContent', 'title'),
                TextInput::make('watched_seconds')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('last_position_seconds')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('watch_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                DateTimePicker::make('last_watched_at'),
                Select::make('status')
                    ->options(ProgressStatus::options())
                    ->required()
                    ->default(ProgressStatus::NotStarted->value),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('student.name')
                    ->label('Student'),
                TextEntry::make('video.display_name')
                    ->label('Video')
                    ->placeholder('-'),
                TextEntry::make('lessonContent.title')
                    ->label('Lesson content')
                    ->placeholder('-'),
                TextEntry::make('watched_seconds')
                    ->numeric(),
                TextEntry::make('last_position_seconds')
                    ->numeric(),
                TextEntry::make('watch_percentage')
                    ->numeric(),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('last_watched_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status'),
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
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('video.display_name')
                    ->label('Video')
                    ->searchable(),
                TextColumn::make('lessonContent.title')
                    ->searchable(),
                TextColumn::make('watched_seconds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_position_seconds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('watch_percentage')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_watched_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
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
            'index' => ListVideoProgress::route('/'),
            'create' => CreateVideoProgress::route('/create'),
            'view' => ViewVideoProgress::route('/{record}'),
            'edit' => EditVideoProgress::route('/{record}/edit'),
        ];
    }
}
