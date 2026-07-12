<?php

namespace App\Filament\Resources\LessonProgress;

use App\Enums\ProgressStatus;
use App\Filament\Resources\LessonProgress\Pages\CreateLessonProgress;
use App\Filament\Resources\LessonProgress\Pages\EditLessonProgress;
use App\Filament\Resources\LessonProgress\Pages\ListLessonProgress;
use App\Filament\Resources\LessonProgress\Pages\ViewLessonProgress;
use App\Models\LessonProgress;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LessonProgressResource extends Resource
{
    protected static ?string $model = LessonProgress::class;

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
                Select::make('lesson_id')
                    ->relationship('lesson', 'title')
                    ->required(),
                Select::make('status')
                    ->options(ProgressStatus::options())
                    ->required()
                    ->default(ProgressStatus::NotStarted->value),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                DateTimePicker::make('last_activity_at'),
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
                TextEntry::make('lesson.title')
                    ->label('Lesson'),
                TextEntry::make('status'),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('last_activity_at')
                    ->dateTime()
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
                TextColumn::make('student.name')
                    ->searchable(),
                TextColumn::make('lesson.title')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_activity_at')
                    ->dateTime()
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
            'index' => ListLessonProgress::route('/'),
            'create' => CreateLessonProgress::route('/create'),
            'view' => ViewLessonProgress::route('/{record}'),
            'edit' => EditLessonProgress::route('/{record}/edit'),
        ];
    }
}
