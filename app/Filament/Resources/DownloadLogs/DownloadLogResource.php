<?php

namespace App\Filament\Resources\DownloadLogs;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\DownloadLogs\Schemas\DownloadLogForm;
use App\Filament\Resources\DownloadLogs\Tables\DownloadLogsTable;
use App\Models\DownloadLog;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DownloadLogResource extends BaseResource
{
    protected static ?string $model = DownloadLog::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return DownloadLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloadLogs::route('/'),
            'create' => Pages\CreateDownloadLog::route('/create'),
            'edit' => Pages\EditDownloadLog::route('/{record}/edit'),
        ];
    }
}
