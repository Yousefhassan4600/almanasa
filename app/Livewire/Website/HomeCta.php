<?php

namespace App\Livewire\Website;

use App\Enums\ProviderType;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class HomeCta extends Component
{
    #[Locked]
    public int $providerId;

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        return view('livewire.website.home-cta', [
            'isVisible' => ! Auth::check(),
            'assetPath' => $provider->type === ProviderType::StandaloneTeacher
                ? config('almanasa.teacher_template_asset_path')
                : config('almanasa.academy_template_asset_path'),
            'isTeacher' => $provider->type === ProviderType::StandaloneTeacher,
            'themeColor' => $provider->websitePrimaryColor(),
            'secondaryThemeColor' => $provider->websiteSecondaryColor(),
        ]);
    }
}
