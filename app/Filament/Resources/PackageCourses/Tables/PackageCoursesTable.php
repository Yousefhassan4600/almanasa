<?php

namespace App\Filament\Resources\PackageCourses\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class PackageCoursesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('package_id')
                ->label('Package Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
