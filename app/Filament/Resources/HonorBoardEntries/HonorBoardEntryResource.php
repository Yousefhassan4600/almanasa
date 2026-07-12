<?php

namespace App\Filament\Resources\HonorBoardEntries;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\HonorBoardEntries\Schemas\HonorBoardEntryForm;
use App\Filament\Resources\HonorBoardEntries\Tables\HonorBoardEntriesTable;
use App\Models\HonorBoardEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class HonorBoardEntryResource extends BaseResource
{
    protected static ?string $model = HonorBoardEntry::class;

    protected static string|UnitEnum|null $navigationGroup = 'Communication & Website';

    public static function form(Schema $schema): Schema
    {
        return HonorBoardEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HonorBoardEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHonorBoardEntries::route('/'),
            'create' => Pages\CreateHonorBoardEntry::route('/create'),
            'edit' => Pages\EditHonorBoardEntry::route('/{record}/edit'),
        ];
    }
}
