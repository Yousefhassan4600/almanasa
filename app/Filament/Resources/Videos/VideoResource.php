<?php

namespace App\Filament\Resources\Videos;

use App\Enums\VideoProcessingStatus;
use App\Enums\VideoProvider;
use App\Enums\VideoVisibility;
use App\Filament\Resources\Videos\Pages\CreateVideo;
use App\Filament\Resources\Videos\Pages\EditVideo;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Filament\Resources\Videos\Pages\ViewVideo;
use App\Models\Video;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Course Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('lesson_content_id')
                    ->relationship('lessonContent', 'title'),
                Select::make('uploaded_by')
                    ->relationship('uploader', 'name')
                    ->label('Uploaded by')
                    ->searchable()
                    ->preload(),
                Select::make('provider')
                    ->options(VideoProvider::options())
                    ->required(),
                TextInput::make('provider_video_id'),
                Textarea::make('video_url')
                    ->columnSpanFull(),
                TextInput::make('duration_seconds')
                    ->numeric(),
                TextInput::make('thumbnail'),
                Select::make('processing_status')
                    ->options(VideoProcessingStatus::options())
                    ->required()
                    ->default(VideoProcessingStatus::Pending->value),
                Select::make('visibility')
                    ->options(VideoVisibility::options())
                    ->required()
                    ->default(VideoVisibility::Private->value),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('lessonContent.title')
                    ->label('Lesson content')
                    ->placeholder('-'),
                TextEntry::make('uploader.name')
                    ->label('Uploaded by')
                    ->placeholder('-'),
                TextEntry::make('provider'),
                TextEntry::make('provider_video_id')
                    ->placeholder('-'),
                TextEntry::make('video_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('duration_seconds')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('thumbnail')
                    ->placeholder('-'),
                TextEntry::make('processing_status'),
                TextEntry::make('visibility'),
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
                TextColumn::make('lessonContent.title')
                    ->searchable(),
                TextColumn::make('uploader.name')
                    ->label('Uploaded by')
                    ->sortable(),
                TextColumn::make('provider')
                    ->searchable(),
                TextColumn::make('provider_video_id')
                    ->searchable(),
                TextColumn::make('duration_seconds')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('thumbnail')
                    ->searchable(),
                TextColumn::make('processing_status')
                    ->searchable(),
                TextColumn::make('visibility')
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
            'index' => ListVideos::route('/'),
            'create' => CreateVideo::route('/create'),
            'view' => ViewVideo::route('/{record}'),
            'edit' => EditVideo::route('/{record}/edit'),
        ];
    }
}
