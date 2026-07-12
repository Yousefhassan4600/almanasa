<?php

namespace App\Filament\Resources\AcademyTeacherGradeSubjects;

use App\Filament\Resources\AcademyTeacherGradeSubjects\Pages\ManageAcademyTeacherGradeSubjects;
use App\Models\AcademyTeacher;
use App\Models\AcademyTeacherGradeSubject;
use App\Models\AccountSubject;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AcademyTeacherGradeSubjectResource extends Resource
{
    protected static ?string $model = AcademyTeacherGradeSubject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Education Setup';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academy_teacher_id')
                    ->options(fn () => AcademyTeacher::query()
                        ->with(['academy', 'teacher'])
                        ->get()
                        ->mapWithKeys(fn (AcademyTeacher $assignment) => [
                            $assignment->id => $assignment->academy?->name.' - '.$assignment->teacher?->name,
                        ])
                        ->all())
                    ->searchable()
                    ->required(),
                Select::make('account_subject_id')
                    ->options(fn () => AccountSubject::query()
                        ->with(['account', 'gradeSubject.grade', 'gradeSubject.subject', 'gradeSubject.track'])
                        ->get()
                        ->mapWithKeys(fn (AccountSubject $coverage) => [$coverage->id => $coverage->name])
                        ->all())
                    ->searchable()
                    ->required(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academyTeacher.academy.name')
                    ->label('Academy')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academyTeacher.teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accountSubject.name')
                    ->label('Grade Subject')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => ManageAcademyTeacherGradeSubjects::route('/'),
        ];
    }
}
