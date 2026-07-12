<?php

namespace App\Filament\Resources\Resources;

use App\Filament\Resources\Resources\Pages\CreateResource;
use App\Filament\Resources\Resources\Pages\EditResource;
use App\Filament\Resources\Resources\Pages\ListResources;
use App\Filament\Resources\Resources\Pages\ViewResource;
use App\Models\LessonContent;
use App\Models\Resource as ResourceModel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MorphToSelect;
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

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Course Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                MorphToSelect::make('resourceable')
                    ->types([
                        MorphToSelect\Type::make(LessonContent::class)
                            ->titleAttribute('title'),
                    ])
                    ->required(),
                Select::make('uploaded_by')
                    ->relationship('uploader', 'name')
                    ->label('Uploaded by')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('file_path'),
                TextInput::make('disk')
                    ->required()
                    ->default('private'),
                TextInput::make('mime_type'),
                TextInput::make('file_size')
                    ->numeric(),
                Toggle::make('is_downloadable')
                    ->required(),
                TextInput::make('external_url')
                    ->url(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('resourceable_display_name')
                    ->label('Resource for'),
                TextEntry::make('uploader.name')
                    ->label('Uploaded by')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('file_path')
                    ->placeholder('-'),
                TextEntry::make('disk'),
                TextEntry::make('mime_type')
                    ->placeholder('-'),
                TextEntry::make('file_size')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_downloadable')
                    ->boolean(),
                TextEntry::make('external_url')
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
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('resourceable_display_name')
                    ->label('Resource for')
                    ->searchable(),
                TextColumn::make('uploader.name')
                    ->label('Uploaded by')
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->searchable(),
                TextColumn::make('disk')
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->searchable(),
                TextColumn::make('file_size')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_downloadable')
                    ->boolean(),
                TextColumn::make('external_url')
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
            'index' => ListResources::route('/'),
            'create' => CreateResource::route('/create'),
            'view' => ViewResource::route('/{record}'),
            'edit' => EditResource::route('/{record}/edit'),
        ];
    }
}
