<?php

namespace App\Filament\Resources\StudentEnrollments;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\StudentEnrollments\Schemas\StudentEnrollmentForm;
use App\Filament\Resources\StudentEnrollments\Tables\StudentEnrollmentsTable;
use App\Models\StudentEnrollment;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StudentEnrollmentResource extends BaseResource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return StudentEnrollmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentEnrollmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentEnrollments::route('/'),
            'create' => Pages\CreateStudentEnrollment::route('/create'),
            'edit' => Pages\EditStudentEnrollment::route('/{record}/edit'),
        ];
    }
}
