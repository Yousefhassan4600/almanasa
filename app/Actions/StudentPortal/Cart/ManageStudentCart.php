<?php

namespace App\Actions\StudentPortal\Cart;

use App\Enums\PurchaseType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Provider;
use App\Models\PurchaseUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ManageStudentCart
{
    public function addCourse(Provider $provider, int $studentUserId, int $courseId, ?int $selectedPurchaseUnitId = null): ?int
    {
        $course = Course::query()
            ->with([
                'prices.purchaseUnit',
                'academyTeacher.teacher.owner:id,first_name,last_name',
                'provider.owner:id,first_name,last_name',
            ])
            ->whereBelongsTo($provider)
            ->whereKey($courseId)
            ->first();

        if (! $course) {
            return null;
        }

        $coursePrice = $this->preferredCoursePrice($course, $selectedPurchaseUnitId);

        if (! $coursePrice) {
            return null;
        }

        DB::transaction(function () use ($provider, $studentUserId, $course, $coursePrice): void {
            $cart = Cart::query()->firstOrCreate(
                [
                    'student_user_id' => $studentUserId,
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

        return $coursePrice->purchase_unit_id;
    }

    public function selectPurchaseUnit(Provider $provider, int $studentUserId, int $purchaseUnitId): ?int
    {
        $purchaseUnit = $this->purchaseUnits($provider)->firstWhere('id', $purchaseUnitId);

        if (! $purchaseUnit) {
            return null;
        }

        $cart = $this->cart($provider, $studentUserId);

        if (! $cart) {
            return $purchaseUnitId;
        }

        DB::transaction(function () use ($cart, $purchaseUnitId): void {
            $cart->loadMissing('items.course.prices.purchaseUnit');

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

        return $purchaseUnitId;
    }

    public function removeItem(Provider $provider, int $studentUserId, int $cartItemId): void
    {
        $cart = $this->cart($provider, $studentUserId);

        if (! $cart) {
            return;
        }

        DB::transaction(function () use ($cart, $cartItemId): void {
            $cart->items()->whereKey($cartItemId)->first()?->delete();
            $this->recalculateCart($cart);
        });
    }

    public function cart(Provider $provider, int $studentUserId): ?Cart
    {
        return Cart::query()
            ->with([
                'items' => fn ($query) => $query->oldest('id'),
                'items.course.accountSubject.gradeSubject.track:id,name',
                'items.course.accountSubject.gradeSubject.subject:id,name',
                'items.course.academyTeacher.teacher.owner:id,first_name,last_name',
                'items.course.provider.owner:id,first_name,last_name',
                'items.course.prices.purchaseUnit',
                'items.coursePrice:id,course_id,purchase_unit_id,price,offer_price',
                'items.purchaseUnit:id,type,name,sort_order,is_active',
            ])
            ->whereBelongsTo($provider)
            ->where('student_user_id', $studentUserId)
            ->where('purchase_type', PurchaseType::SingleCourse->value)
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, PurchaseUnit>
     */
    public function purchaseUnits(Provider $provider): Collection
    {
        return PurchaseUnit::query()
            ->where('is_active', true)
            ->whereHas('prices.course', fn ($query) => $query->whereBelongsTo($provider))
            ->oldest('sort_order')
            ->oldest('id')
            ->get(['id', 'type', 'name', 'sort_order', 'is_active']);
    }

    private function preferredCoursePrice(Course $course, ?int $selectedPurchaseUnitId = null): ?CoursePrice
    {
        if ($selectedPurchaseUnitId) {
            $selectedPrice = $course->prices
                ->first(fn (CoursePrice $price): bool => (int) $price->purchase_unit_id === (int) $selectedPurchaseUnitId);

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

    private function recalculateCart(Cart $cart): void
    {
        $subtotal = (float) $cart->items()->sum('total');

        $cart->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }
}
