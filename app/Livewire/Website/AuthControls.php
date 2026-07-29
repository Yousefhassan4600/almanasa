<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Layout\CountCartItems;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class AuthControls extends Component
{
    #[Locked]
    public int $providerId;

    #[Locked]
    public string $placement = 'desktop';

    #[Locked]
    public bool $logoutOnly = false;

    private CountCartItems $countCartItems;

    public function boot(CountCartItems $countCartItems): void
    {
        $this->countCartItems = $countCartItems;
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/login', navigate: false);
    }

    #[On('cart-updated')]
    public function refreshCartCount(): void {}

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $hasCompletedProfile = Auth::check() && Auth::user()?->studentProfile()->exists();

        return view('livewire.website.auth-controls', [
            'hasCompletedProfile' => $hasCompletedProfile,
            'logoutOnly' => $this->logoutOnly,
            'themeColor' => $provider->websitePrimaryColor(),
            'isDesktop' => $this->placement === 'desktop',
            'cartItemsCount' => $this->countCartItems->handle($provider, Auth::id(), $hasCompletedProfile),
        ]);
    }
}
