<?php

namespace App\Filament\Resources\StudentProfiles;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\StudentProfiles\Schemas\StudentProfileForm;
use App\Filament\Resources\StudentProfiles\Tables\StudentProfilesTable;
use App\Models\StudentProfile;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StudentProfileResource extends BaseResource
{
    protected static ?string $model = StudentProfile::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return StudentProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentProfiles::route('/'),
            'create' => Pages\CreateStudentProfile::route('/create'),
            'edit' => Pages\EditStudentProfile::route('/{record}/edit'),
        ];
    }
}
