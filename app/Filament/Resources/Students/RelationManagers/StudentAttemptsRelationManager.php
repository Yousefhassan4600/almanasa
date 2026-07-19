<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\StudentAttempts\Tables\StudentAttemptsTable;
use Filament\Tables\Table;

class StudentAttemptsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'studentAttempts';

    public function table(Table $table): Table
    {
        return StudentAttemptsTable::configure($table);
    }
}
