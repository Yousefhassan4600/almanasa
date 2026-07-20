<?php

namespace App\Filament\Clusters;

use App\Filament\Support\CurrentAccount;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class Locations extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return 'Project Data';
    }

    public static function canAccess(): bool
    {
        return CurrentAccount::isSaasOwner();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return CurrentAccount::isSaasOwner();
    }

    public static function canAccessClusteredComponents(): bool
    {
        return CurrentAccount::isSaasOwner();
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin.clusters.Locations');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('admin.clusters.Locations');
    }

    public function getTitle(): string
    {
        return __('admin.clusters.Locations');
    }

    public function getHeading(): string
    {
        return __('admin.clusters.Locations');
    }
}
