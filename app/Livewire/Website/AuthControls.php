<?php

namespace App\Livewire\Website;

use App\Enums\PurchaseType;
use App\Models\Cart;
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

        return view('livewire.website.auth-controls', [
            'hasCompletedProfile' => Auth::check() && Auth::user()?->studentProfile()->exists(),
            'logoutOnly' => $this->logoutOnly,
            'themeColor' => $provider->websitePrimaryColor(),
            'isDesktop' => $this->placement === 'desktop',
            'cartItemsCount' => $this->cartItemsCount($provider),
        ]);
    }

    private function cartItemsCount(Provider $provider): int
    {
        if (! Auth::check() || ! Auth::user()?->studentProfile()->exists()) {
            return 0;
        }

        return (int) (Cart::query()
            ->whereBelongsTo($provider)
            ->where('student_user_id', Auth::id())
            ->where('purchase_type', PurchaseType::SingleCourse->value)
            ->withCount('items')
            ->latest()
            ->first()
            ?->items_count ?? 0);
    }
}
