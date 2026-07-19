<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class EducationCatalog extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return 'Project Data';
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Education Catalog';
    }

    public static function getClusterBreadcrumb(): string
    {
        return 'Education Catalog';
    }

    public function getTitle(): string
    {
        return 'Education Catalog';
    }

    public function getHeading(): string
    {
        return 'Education Catalog';
    }
}
