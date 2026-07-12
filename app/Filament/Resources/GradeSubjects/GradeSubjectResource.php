<?php

namespace App\Filament\Resources\GradeSubjects;

use App\Filament\Resources\GradeSubjects\Pages\ManageGradeSubjects;
use App\Models\GradeSubject;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GradeSubjectResource extends Resource
{
    protected static ?string $model = GradeSubject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Education Setup';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_id')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->required(),
                Select::make('track_id')
                    ->relationship('track', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grade.name')
                    ->label('Grade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('track.name')
                    ->label('Track')
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
            'index' => ManageGradeSubjects::route('/'),
        ];
    }
}
