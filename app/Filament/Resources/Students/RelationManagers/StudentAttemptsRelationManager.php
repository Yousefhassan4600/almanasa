<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\StudentAttempts\Tables\StudentAttemptsTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentAttemptsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'studentAttempts';

    public function table(Table $table): Table
    {
        return StudentAttemptsTable::configure($table)
            ->modifyQueryUsing(fn (Builder $query): Builder => $this->scopeToCurrentTeacher($query));
    }

    private function scopeToCurrentTeacher(Builder $query): Builder
    {
        $account = CurrentAccount::account();

        if (! $account || ! CurrentAccount::isAcademyTeacher()) {
            return $query;
        }

        return $query->whereHas('course.academyTeacher', fn (Builder $query): Builder => $query
            ->where('teacher_account_id', $account->id));
    }
}
