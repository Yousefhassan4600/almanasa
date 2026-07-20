<?php

namespace App\Livewire\Website;

use App\Enums\PurchaseType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Order;
use App\Models\OrderStatusType;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ProviderPaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public ?TemporaryUploadedFile $transferImage = null;

    public ?string $transactionReference = null;

    public ?string $submittedOrderNumber = null;

    public function mount(): void
    {
        $this->courseId ??= request()->integer('course') ?: null;

        if ($this->courseId) {
            $this->addCourseToCart($this->courseId);
            $this->courseId = null;
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $this->selectedProviderPaymentMethodId = $this->paymentMethods($provider)->first()?->id;
    }

    public function selectPaymentMethod(int $providerPaymentMethodId): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        if (! $this->paymentMethods($provider)->contains('id', $providerPaymentMethodId)) {
            return;
        }

        $this->selectedProviderPaymentMethodId = $providerPaymentMethodId;
        $this->resetValidation();
    }

    public function submitOrder(): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $paymentMethod = $this->paymentMethods($provider)->firstWhere('id', $this->selectedProviderPaymentMethodId);
        $cart = $this->cart($provider);

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

        $cart->loadMissing('items');

        $this->submittedOrderNumber = DB::transaction(function () use ($provider, $paymentMethod, $cart): string {
            $transferImagePath = $this->transferImage?->store('payment-proofs', 'public');
            $order = Order::query()->create([
                'provider_id' => $provider->id,
                'student_user_id' => Auth::id(),
                'cart_id' => $cart->id,
                'order_number' => $this->nextOrderNumber($provider),
                'purchase_type' => PurchaseType::SingleCourse->value,
                'subtotal' => $cart->subtotal,
                'total' => $cart->total,
            ]);

            $cart->items->each(function (CartItem $item) use ($order): void {
                $order->items()->create([
                    'course_id' => $item->course_id,
                    'course_price_id' => $item->course_price_id,
                    'purchase_unit_id' => $item->purchase_unit_id,
                    'purchase_type' => $item->purchase_type,
                    'title' => $item->title,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            });

            $order->statuses()->create([
                'order_status_type_id' => $this->pendingStatusType()->id,
                'is_current' => true,
                'status_at' => now(),
                'created_by_user_id' => Auth::id(),
                'notes' => 'Waiting for provider approval.',
            ]);

            Payment::query()->create([
                'order_id' => $order->id,
                'provider_id' => $provider->id,
                'student_user_id' => Auth::id(),
                'provider_payment_method_id' => $paymentMethod->id,
                'transaction_reference' => $this->transactionReference,
                'transfer_image' => $transferImagePath,
                'is_paid' => false,
            ]);

            return $order->order_number;
        });

        $this->transferImage = null;
        $this->transactionReference = null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = $this->cart($provider);
        $paymentMethods = $this->paymentMethods($provider);

        return view('livewire.website.checkout-page', [
            'provider' => $provider,
            'cart' => $cart,
            'items' => $cart?->items ?? collect(),
            'paymentMethods' => $paymentMethods,
            'selectedPaymentMethod' => $paymentMethods->firstWhere('id', $this->selectedProviderPaymentMethodId),
        ]);
    }

    private function addCourseToCart(int $courseId): void
    {
        if (! Auth::check()) {
            return;
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $course = Course::query()
            ->with('prices.purchaseUnit')
            ->whereBelongsTo($provider)
            ->whereKey($courseId)
            ->first();

        if (! $course) {
            return;
        }

        $coursePrice = $course->prices
            ->filter(fn (CoursePrice $price): bool => (bool) $price->purchaseUnit?->is_active)
            ->sort(function (CoursePrice $firstPrice, CoursePrice $secondPrice): int {
                return [
                    $firstPrice->purchaseUnit?->sort_order ?? PHP_INT_MAX,
                    $firstPrice->purchaseUnit?->id ?? PHP_INT_MAX,
                ] <=> [
                    $secondPrice->purchaseUnit?->sort_order ?? PHP_INT_MAX,
                    $secondPrice->purchaseUnit?->id ?? PHP_INT_MAX,
                ];
            })
            ->first();

        if (! $coursePrice) {
            return;
        }

        DB::transaction(function () use ($provider, $course, $coursePrice): void {
            $cart = Cart::query()->firstOrCreate(
                [
                    'student_user_id' => Auth::id(),
                    'provider_id' => $provider->id,
                    'purchase_type' => PurchaseType::SingleCourse->value,
                ],
                [
                    'subtotal' => 0,
                    'total' => 0,
                ],
            );

            $cartItem = $cart->items()
                ->withTrashed()
                ->whereBelongsTo($course)
                ->first();

            if ($cartItem?->trashed()) {
                $cartItem->restore();
            }

            ($cartItem ?: $cart->items()->make(['course_id' => $course->id]))->fill([
                'course_price_id' => $coursePrice->id,
                'purchase_unit_id' => $coursePrice->purchase_unit_id,
                'purchase_type' => PurchaseType::SingleCourse->value,
                'title' => $course->getTranslation('title', 'ar', false) ?: $course->title,
                'unit_price' => $coursePrice->price,
                'total' => $coursePrice->price,
            ])->save();

            $this->recalculateCart($cart);
        });
    }

    private function cart(Provider $provider): ?Cart
    {
        if (! Auth::check()) {
            return null;
        }

        return Cart::query()
            ->with([
                'items' => fn ($query) => $query->oldest('id'),
                'items.course.accountSubject.gradeSubject.subject:id,name,track_id',
                'items.purchaseUnit:id,type,name,sort_order,is_active',
            ])
            ->whereBelongsTo($provider)
            ->where('student_user_id', Auth::id())
            ->where('purchase_type', PurchaseType::SingleCourse->value)
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, ProviderPaymentMethod>
     */
    private function paymentMethods(Provider $provider): Collection
    {
        return ProviderPaymentMethod::query()
            ->with('paymentMethod:id,name,slug,image,is_active,is_bank,require_proof,is_code,sort_order')
            ->whereBelongsTo($provider)
            ->whereHas('paymentMethod', fn ($query) => $query->where('is_active', true))
            ->join('payment_methods', 'payment_methods.id', '=', 'provider_payment_methods.payment_method_id')
            ->orderBy('payment_methods.sort_order')
            ->orderBy('provider_payment_methods.id')
            ->select('provider_payment_methods.*')
            ->get();
    }

    private function recalculateCart(Cart $cart): void
    {
        $subtotal = (float) $cart->items()->sum('total');

        $cart->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    private function pendingStatusType(): OrderStatusType
    {
        return OrderStatusType::query()->firstOrCreate([
            'slug' => 'pending',
        ], [
            'name' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function nextOrderNumber(Provider $provider): string
    {
        do {
            $orderNumber = 'ORD-'.$provider->id.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
