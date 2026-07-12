<?php

namespace App\Filament\Resources\AcademyTeacherGradeSubjects\Schemas;

use App\Models\AcademyTeacher;
use App\Models\AccountSubject;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademyTeacherGradeSubjectForm
{
    public static function configure(Schema $schema): Schema
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
}
