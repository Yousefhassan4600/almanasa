<?php

namespace App\Filament\Resources\AcademyTeachers;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AcademyTeachers\Schemas\AcademyTeacherForm;
use App\Filament\Resources\AcademyTeachers\Tables\AcademyTeachersTable;
use App\Filament\Support\CurrentAccount;
use App\Models\AcademyTeacher;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AcademyTeacherResource extends BaseResource
{
    protected static ?string $model = AcademyTeacher::class;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Accounts';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AcademyTeacherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademyTeachersTable::configure($table);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::shouldRegisterNavigation();
    }

    public static function canViewAny(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canViewAny();
    }

    public static function canCreate(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canCreate();
    }

    public static function canEdit(Model $record): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canEdit($record);
    }

    public static function canDelete(Model $record): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canDelete($record);
    }

    public static function canDeleteAny(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canDeleteAny();
    }

    public static function canForceDelete(Model $record): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canForceDelete($record);
    }

    public static function canForceDeleteAny(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canForceDeleteAny();
    }

    public static function canRestore(Model $record): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canRestore($record);
    }

    public static function canRestoreAny(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canRestoreAny();
    }

    public static function canReorder(): bool
    {
        return ! CurrentAccount::isStandaloneTeacher() && parent::canReorder();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (CurrentAccount::isStandaloneTeacher()) {
            return $query->whereRaw('1 = 0');
        }

        if (CurrentAccount::isSaasOwner()) {
            return $query;
        }

        $providerId = CurrentAccount::providerId();

        return $providerId
            ? $query->where('provider_id', $providerId)
            : $query->whereRaw('1 = 0');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademyTeachers::route('/'),
        ];
    }
}
