<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class OrdersCatalog extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return 'Project Data';
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Orders Catalog';
    }

    public static function getClusterBreadcrumb(): string
    {
        return 'Orders Catalog';
    }

    public function getTitle(): string
    {
        return 'Orders Catalog';
    }

    public function getHeading(): string
    {
        return 'Orders Catalog';
    }
}
