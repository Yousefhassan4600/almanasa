<?php

namespace App\Filament\Resources\ParentStudents\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ParentStudentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('parent_user_id')
                ->label('Parent User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('relation')
                ->label('Relation')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_primary')
                ->label('Is Primary')
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
