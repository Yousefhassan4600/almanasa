<?php

namespace App\Actions\StudentPortal\Layout;

use App\Enums\ProviderType;
use App\Models\Provider;

class LoadHomeCta
{
    /**
     * @return array{isVisible: bool, assetPath: mixed, isTeacher: bool, themeColor: string, secondaryThemeColor: string}
     */
    public function handle(Provider $provider, bool $isAuthenticated): array
    {
        return [
            'isVisible' => ! $isAuthenticated,
            'assetPath' => $provider->type === ProviderType::StandaloneTeacher
                ? config('almanasa.teacher_template_asset_path')
                : config('almanasa.academy_template_asset_path'),
            'isTeacher' => $provider->type === ProviderType::StandaloneTeacher,
            'themeColor' => $provider->websitePrimaryColor(),
            'secondaryThemeColor' => $provider->websiteSecondaryColor(),
        ];
    }
}
