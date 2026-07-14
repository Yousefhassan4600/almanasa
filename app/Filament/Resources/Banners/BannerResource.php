<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Banners\Schemas\BannerForm;
use App\Filament\Resources\Banners\Tables\BannersTable;
use App\Models\Banner;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BannerResource extends BaseResource
{
    protected static ?string $model = Banner::class;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BannersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
        ];
    }
}
