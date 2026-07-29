<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Layout\LoadHomeCta;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class HomeCta extends Component
{
    #[Locked]
    public int $providerId;

    private LoadHomeCta $loadHomeCta;

    public function boot(LoadHomeCta $loadHomeCta): void
    {
        $this->loadHomeCta = $loadHomeCta;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        return view('livewire.website.home-cta', $this->loadHomeCta->handle($provider, Auth::check()));
    }
}
