<?php

namespace App\Livewire\Website;

use App\Enums\ProviderType;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AuthControls extends Component
{
    #[Locked]
    public int $providerId;

    #[Locked]
    public string $placement = 'desktop';

    #[Locked]
    public bool $logoutOnly = false;

    public function logout(): mixed
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/login', navigate: false);
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        return view('livewire.website.auth-controls', [
            'hasCompletedProfile' => Auth::check() && Auth::user()?->studentProfile()->exists(),
            'logoutOnly' => $this->logoutOnly,
            'themeColor' => $provider->type === ProviderType::StandaloneTeacher ? '#FEB008' : '#5D3FD3',
            'isDesktop' => $this->placement === 'desktop',
        ]);
    }
}
