<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class Locations extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return 'Project Data';
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Locations';
    }

    public static function getClusterBreadcrumb(): string
    {
        return 'Locations';
    }

    public function getTitle(): string
    {
        return 'Locations';
    }

    public function getHeading(): string
    {
        return 'Locations';
    }
}
