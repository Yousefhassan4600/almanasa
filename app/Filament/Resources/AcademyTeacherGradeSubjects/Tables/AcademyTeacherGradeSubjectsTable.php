<?php

namespace App\Filament\Resources\AcademyTeacherGradeSubjects\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AcademyTeacherGradeSubjectsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
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
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
