<?php

namespace App\Filament\Resources\GradeSubjects;

use App\Filament\Resources\GradeSubjects\Pages\CreateGradeSubject;
use App\Filament\Resources\GradeSubjects\Pages\EditGradeSubject;
use App\Filament\Resources\GradeSubjects\Pages\ListGradeSubjects;
use App\Filament\Resources\GradeSubjects\Pages\ViewGradeSubject;
use App\Models\GradeSubject;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
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

class GradeSubjectResource extends Resource
{
    protected static ?string $model = GradeSubject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Education Catalog';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_id')
                    ->relationship('grade', 'name')
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('education_track_id')
                    ->relationship('educationTrack', 'name'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('grade.name')
                    ->label('Grade'),
                TextEntry::make('subject.name')
                    ->label('Subject'),
                TextEntry::make('educationTrack.name')
                    ->label('Education track')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
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
                TextColumn::make('grade.name')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('educationTrack.name')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => ListGradeSubjects::route('/'),
            'create' => CreateGradeSubject::route('/create'),
            'view' => ViewGradeSubject::route('/{record}'),
            'edit' => EditGradeSubject::route('/{record}/edit'),
        ];
    }
}
