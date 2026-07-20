<?php

namespace App\Filament\Resources\Tracks;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\Tracks\Tables\TracksTable;
use App\Models\Track;
use Filament\Tables\Table;

class TrackResource extends BaseResource
{
    protected static ?string $model = Track::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return TracksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTracks::route('/'),
        ];
    }
}
