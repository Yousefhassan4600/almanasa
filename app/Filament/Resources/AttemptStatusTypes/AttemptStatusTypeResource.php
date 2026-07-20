<?php

namespace App\Filament\Resources\AttemptStatusTypes;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\AttemptStatusTypes\Tables\AttemptStatusTypesTable;
use App\Models\AttemptStatusType;
use Filament\Tables\Table;

class AttemptStatusTypeResource extends BaseResource
{
    protected static ?string $model = AttemptStatusType::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return AttemptStatusTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttemptStatusTypes::route('/'),
        ];
    }
}
