<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Cart\ManageStudentCart;
use App\Actions\StudentPortal\Checkout\ListProviderPaymentMethods;
use App\Actions\StudentPortal\Checkout\SubmitCheckoutOrder;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class CheckoutPage extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $providerId;

    #[Url(as: 'course')]
    public ?int $courseId = null;

    public ?int $selectedProviderPaymentMethodId = null;

    public ?int $selectedPurchaseUnitId = null;

    public ?TemporaryUploadedFile $transferImage = null;

    public ?string $transactionReference = null;

    public ?string $submittedOrderNumber = null;

    private ManageStudentCart $manageStudentCart;

    private ListProviderPaymentMethods $listProviderPaymentMethods;

    private SubmitCheckoutOrder $submitCheckoutOrder;

    public function boot(
        ManageStudentCart $manageStudentCart,
        ListProviderPaymentMethods $listProviderPaymentMethods,
        SubmitCheckoutOrder $submitCheckoutOrder,
    ): void {
        $this->manageStudentCart = $manageStudentCart;
        $this->listProviderPaymentMethods = $listProviderPaymentMethods;
        $this->submitCheckoutOrder = $submitCheckoutOrder;
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
            ?: $this->selectedPurchaseUnitId
            ?: $this->manageStudentCart->purchaseUnits($provider)->first()?->id;

        $this->selectedProviderPaymentMethodId = $this->listProviderPaymentMethods->handle($provider)->first()?->id;
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

    public function selectPaymentMethod(int $providerPaymentMethodId): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        if (! $this->listProviderPaymentMethods->handle($provider)->contains('id', $providerPaymentMethodId)) {
            return;
        }

        $this->selectedProviderPaymentMethodId = $providerPaymentMethodId;
        $this->resetValidation();
    }

    public function submitOrder(): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $paymentMethod = $this->listProviderPaymentMethods->handle($provider)->firstWhere('id', $this->selectedProviderPaymentMethodId);
        $cart = Auth::check() ? $this->manageStudentCart->cart($provider, Auth::id()) : null;

        if (! $paymentMethod || ! $cart || $cart->items->isEmpty()) {
            $this->addError('checkout', 'لا يمكن إتمام الدفع قبل اختيار وسيلة دفع وإضافة مواد للسلة.');

            return;
        }

        $rules = [
            'transactionReference' => ['nullable', 'string', 'max:255'],
        ];

        if ($paymentMethod->paymentMethod?->require_proof) {
            $rules['transferImage'] = ['required', 'image', 'max:2048'];
        } else {
            $rules['transferImage'] = ['nullable', 'image', 'max:2048'];
        }

        $this->validate($rules, [
            'transferImage.required' => 'يرجى رفع صورة التحويل.',
            'transferImage.image' => 'صورة التحويل يجب أن تكون ملف صورة.',
            'transferImage.max' => 'حجم صورة التحويل يجب ألا يتجاوز 2MB.',
        ]);

        $this->submittedOrderNumber = $this->submitCheckoutOrder->handle(
            $provider,
            Auth::id(),
            $paymentMethod,
            $cart,
            $this->transferImage,
            $this->transactionReference,
        );

        $this->transferImage = null;
        $this->transactionReference = null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = Auth::check() ? $this->manageStudentCart->cart($provider, Auth::id()) : null;
        $paymentMethods = $this->listProviderPaymentMethods->handle($provider);

        return view('livewire.website.checkout-page', [
            'provider' => $provider,
            'cart' => $cart,
            'items' => $cart?->items ?? collect(),
            'purchaseUnits' => $this->manageStudentCart->purchaseUnits($provider),
            'selectedPurchaseUnitId' => $this->selectedPurchaseUnitId,
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $paymentMethods->firstWhere('id', $this->selectedProviderPaymentMethodId),
        ]);
    }
}
