<?php

namespace App\Filament\Resources\StudentEnrollments\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class StudentEnrollmentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('package_id')
                ->label('Package Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('subscription_id')
                ->label('Subscription Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            TextColumn::make('started_at')
                ->label('Started At')
                ->searchable()
                ->sortable(),
            TextColumn::make('expires_at')
                ->label('Expires At')
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
