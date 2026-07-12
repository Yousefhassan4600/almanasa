<?php

namespace App\Filament\Resources\Tracks;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Tracks\Schemas\TrackForm;
use App\Filament\Resources\Tracks\Tables\TracksTable;
use App\Models\Track;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TrackResource extends BaseResource
{
    protected static ?string $model = Track::class;

    protected static string|UnitEnum|null $navigationGroup = BaseResource::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?string $navigationParentItem = BaseResource::EDUCATION_CATALOG_NAVIGATION_PARENT;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TrackForm::configure($schema);
    }

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
            'create' => Pages\CreateTrack::route('/create'),
            'edit' => Pages\EditTrack::route('/{record}/edit'),
        ];
    }
}
