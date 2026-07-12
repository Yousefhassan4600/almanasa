<?php

namespace App\Filament\Resources\Questions;

use App\Enums\PublishingStatus;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Pages\ViewQuestion;
use App\Models\Question;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Assessments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('created_by')
                    ->relationship('creator', 'name')
                    ->label('Created by')
                    ->searchable()
                    ->preload(),
                Select::make('type')
                    ->options(QuestionType::options())
                    ->required(),
                Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Textarea::make('explanation_text')
                    ->columnSpanFull(),
                FileUpload::make('explanation_image')
                    ->image(),
                Select::make('difficulty')
                    ->options(QuestionDifficulty::options()),
                TextInput::make('default_score')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('topic'),
                Select::make('status')
                    ->options(PublishingStatus::options())
                    ->required()
                    ->default(PublishingStatus::Draft->value),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('creator.name')
                    ->label('Created by')
                    ->placeholder('-'),
                TextEntry::make('type'),
                TextEntry::make('text')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('explanation_text')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('explanation_image')
                    ->placeholder('-'),
                TextEntry::make('difficulty')
                    ->placeholder('-'),
                TextEntry::make('default_score')
                    ->numeric(),
                TextEntry::make('topic')
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
                TextColumn::make('creator.name')
                    ->label('Created by')
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                ImageColumn::make('image'),
                ImageColumn::make('explanation_image'),
                TextColumn::make('difficulty')
                    ->searchable(),
                TextColumn::make('default_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('topic')
                    ->searchable(),
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
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'view' => ViewQuestion::route('/{record}'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}
