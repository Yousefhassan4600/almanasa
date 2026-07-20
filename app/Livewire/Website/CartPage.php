<?php

namespace App\Livewire\Website;

use App\Enums\PurchaseType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Provider;
use App\Models\PurchaseUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function mount(): void
    {
        $this->courseId ??= request()->integer('course') ?: null;

        if ($this->courseId) {
            $this->addCourseToCart($this->courseId);
            $this->courseId = null;
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = $this->cart($provider);

        $this->selectedPurchaseUnitId = $cart?->items()->value('purchase_unit_id')
            ?: $this->purchaseUnits($provider)->first()?->id;
    }

    public function selectPurchaseUnit(int $purchaseUnitId): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $purchaseUnit = $this->purchaseUnits($provider)->firstWhere('id', $purchaseUnitId);

        if (! $purchaseUnit) {
            return;
        }

        $cart = $this->cart($provider);

        if (! $cart) {
            $this->selectedPurchaseUnitId = $purchaseUnitId;

            return;
        }

        DB::transaction(function () use ($cart, $purchaseUnitId): void {
            $cart->loadMissing('items.course.prices');

            $cart->items->each(function (CartItem $item) use ($purchaseUnitId): void {
                $price = $item->course->prices->firstWhere('purchase_unit_id', $purchaseUnitId);

                if (! $price) {
                    return;
                }

                $item->update([
                    'course_price_id' => $price->id,
                    'purchase_unit_id' => $purchaseUnitId,
                    'unit_price' => $price->price,
                    'total' => $price->price,
                ]);
            });

            $this->recalculateCart($cart);
        });

        $this->selectedPurchaseUnitId = $purchaseUnitId;
    }

    public function removeItem(int $cartItemId): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = $this->cart($provider);

        if (! $cart) {
            return;
        }

        DB::transaction(function () use ($cart, $cartItemId): void {
            $cart->items()->whereKey($cartItemId)->first()?->delete();
            $this->recalculateCart($cart);
        });

        $this->dispatch('cart-updated');
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $cart = $this->cart($provider);

        return view('livewire.website.cart-page', [
            'provider' => $provider,
            'cart' => $cart,
            'items' => $cart?->items ?? collect(),
            'purchaseUnits' => $this->purchaseUnits($provider),
            'selectedPurchaseUnitId' => $this->selectedPurchaseUnitId,
        ]);
    }

    private function addCourseToCart(int $courseId): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $course = Course::query()
            ->with([
                'prices.purchaseUnit',
                'academyTeacher.teacher.owner:id,first_name,last_name',
                'provider.owner:id,first_name,last_name',
            ])
            ->whereBelongsTo($provider)
            ->whereKey($courseId)
            ->first();

        if (! $course || ! Auth::check()) {
            return;
        }

        $coursePrice = $this->preferredCoursePrice($course);

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
            $this->selectedPurchaseUnitId = $coursePrice->purchase_unit_id;
        });

        $this->dispatch('cart-updated');
    }

    private function preferredCoursePrice(Course $course): ?CoursePrice
    {
        if ($this->selectedPurchaseUnitId) {
            $selectedPrice = $course->prices
                ->first(fn (CoursePrice $price): bool => (int) $price->purchase_unit_id === (int) $this->selectedPurchaseUnitId);

            if ($selectedPrice) {
                return $selectedPrice;
            }
        }

        return $course->prices
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
                'items.course.academyTeacher.teacher.owner:id,first_name,last_name',
                'items.course.provider.owner:id,first_name,last_name',
                'items.coursePrice:id,course_id,purchase_unit_id,price,offer_price',
                'items.purchaseUnit:id,type,name,sort_order,is_active',
            ])
            ->whereBelongsTo($provider)
            ->where('student_user_id', Auth::id())
            ->where('purchase_type', PurchaseType::SingleCourse->value)
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, PurchaseUnit>
     */
    private function purchaseUnits(Provider $provider): Collection
    {
        return PurchaseUnit::query()
            ->where('is_active', true)
            ->whereHas('prices.course', fn ($query) => $query->whereBelongsTo($provider))
            ->oldest('sort_order')
            ->oldest('id')
            ->get(['id', 'type', 'name', 'sort_order', 'is_active']);
    }

    private function recalculateCart(Cart $cart): void
    {
        $subtotal = (float) $cart->items()->sum('total');

        $cart->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }
}
