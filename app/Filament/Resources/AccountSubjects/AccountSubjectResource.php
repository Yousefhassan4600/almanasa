<?php

namespace App\Filament\Resources\AccountSubjects;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AccountSubjects\Schemas\AccountSubjectForm;
use App\Filament\Resources\AccountSubjects\Tables\AccountSubjectsTable;
use App\Models\AccountSubject;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AccountSubjectResource extends BaseResource
{
    protected static ?string $model = AccountSubject::class;

    protected static string|UnitEnum|null $navigationGroup = 'Education Setup';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return AccountSubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccountSubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountSubjects::route('/'),
            'create' => Pages\CreateAccountSubject::route('/create'),
            'edit' => Pages\EditAccountSubject::route('/{record}/edit'),
        ];
    }
}
