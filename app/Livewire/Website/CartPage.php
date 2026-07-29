<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Cart\ManageStudentCart;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class CartPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'course')]
    public ?int $courseId = null;

    public ?int $selectedPurchaseUnitId = null;

    private ManageStudentCart $manageStudentCart;

    public function boot(ManageStudentCart $manageStudentCart): void
    {
        $this->manageStudentCart = $manageStudentCart;
    }

    public function mount(): void
    {
        $this->courseId ??= request()->integer('course') ?: null;
        $provider = Provider::query()->findOrFail($this->providerId);

        if ($this->courseId && Auth::check()) {
            $this->selectedPurchaseUnitId = $this->manageStudentCart->addCourse(
                $provider,
                Auth::id(),
                $this->courseId,
                $this->selectedPurchaseUnitId,
            ) ?: $this->selectedPurchaseUnitId;
            $this->courseId = null;
            $this->dispatch('cart-updated');
        }

        $cart = Auth::check() ? $this->manageStudentCart->cart($provider, Auth::id()) : null;

        $this->selectedPurchaseUnitId = $cart?->items()->value('purchase_unit_id')
            ?: $this->manageStudentCart->purchaseUnits($provider)->first()?->id;
    }

    public function selectPurchaseUnit(int $purchaseUnitId): void
    {
        if (! Auth::check()) {
            return;
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $selectedPurchaseUnitId = $this->manageStudentCart->selectPurchaseUnit($provider, Auth::id(), $purchaseUnitId);

        $this->selectedPurchaseUnitId = $selectedPurchaseUnitId ?: $this->selectedPurchaseUnitId;
    }

    public function removeItem(int $cartItemId): void
    {
        if (! Auth::check()) {
            return;
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $this->manageStudentCart->removeItem($provider, Auth::id(), $cartItemId);

        $this->dispatch('cart-updated');
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = Auth::check() ? $this->manageStudentCart->cart($provider, Auth::id()) : null;

        return view('livewire.website.cart-page', [
            'provider' => $provider,
            'cart' => $cart,
            'items' => $cart?->items ?? collect(),
            'purchaseUnits' => $this->manageStudentCart->purchaseUnits($provider),
            'selectedPurchaseUnitId' => $this->selectedPurchaseUnitId,
        ]);
    }
}
