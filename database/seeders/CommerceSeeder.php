<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodSlugs;
use App\Enums\PurchaseType;
use App\Enums\PurchaseUnitType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusType;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\ProviderPaymentMethod;
use App\Models\Subscription;
use App\Models\User;

class CommerceSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->orderStatusTypes();
        $this->providerPaymentMethods();

        $academyProvider = Provider::query()->where('slug', 'future-stars-academy')->firstOrFail();
        $studentUser = User::query()->where('phone', '01000000004')->firstOrFail();
        $academyOwner = User::query()->where('phone', '01000000001')->firstOrFail();
        $academyCourse = $this->academyCourse($academyProvider);
        $coursePrice = CoursePrice::query()
            ->where('course_id', $academyCourse->id)
            ->whereHas('purchaseUnit', fn ($query) => $query->where('type', PurchaseUnitType::Month->value))
            ->firstOrFail();
        $providerPaymentMethod = $this->providerPaymentMethod($academyProvider);
        $singleCoursePrice = (float) $coursePrice->price;
        $allSubjectsOfferPrice = (float) ($coursePrice->offer_price ?? $coursePrice->price);

        $cart = Cart::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'purchase_type' => PurchaseType::AllSubjectsOffer,
        ], [
            'subtotal' => $allSubjectsOfferPrice,
            'total' => $allSubjectsOfferPrice,
        ]);

        CartItem::query()->firstOrCreate([
            'cart_id' => $cart->id,
            'course_id' => $academyCourse->id,
        ], [
            'course_price_id' => $coursePrice->id,
            'purchase_unit_id' => $coursePrice->purchase_unit_id,
            'purchase_type' => PurchaseType::AllSubjectsOffer,
            'title' => $academyCourse->title,
            'unit_price' => $allSubjectsOfferPrice,
            'total' => $allSubjectsOfferPrice,
        ]);

        $order = Order::query()->firstOrCreate([
            'order_number' => 'ORD-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'cart_id' => $cart->id,
            'purchase_type' => PurchaseType::AllSubjectsOffer,
            'subtotal' => $allSubjectsOfferPrice,
            'total' => $allSubjectsOfferPrice,
        ]);

        $this->orderStatus($order, 'paid', $academyOwner);

        $orderItem = OrderItem::query()->firstOrCreate([
            'order_id' => $order->id,
            'course_id' => $academyCourse->id,
        ], [
            'course_price_id' => $coursePrice->id,
            'purchase_unit_id' => $coursePrice->purchase_unit_id,
            'purchase_type' => PurchaseType::AllSubjectsOffer,
            'title' => $academyCourse->title,
            'unit_price' => $allSubjectsOfferPrice,
            'total' => $allSubjectsOfferPrice,
        ]);

        Subscription::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'course_id' => $academyCourse->id,
            'order_item_id' => $orderItem->id,
        ], [
            'purchase_unit_id' => $coursePrice->purchase_unit_id,
            'purchase_type' => PurchaseType::AllSubjectsOffer,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        Payment::query()->firstOrCreate([
            'order_id' => $order->id,
            'transaction_reference' => 'PAY-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'provider_payment_method_id' => $providerPaymentMethod->id,
            'is_paid' => true,
        ]);

        $this->singleCourseCart($academyProvider, $studentUser, $academyCourse, $coursePrice, $singleCoursePrice);
    }

    private function orderStatusTypes(): void
    {
        foreach (
            [
                'pending' => ['Pending', 'قيد الانتظار', 1],
                'paid' => ['Paid', 'مدفوع', 2],
                'cancelled' => ['Cancelled', 'ملغي', 3],
                'refunded' => ['Refunded', 'مسترد', 4],
            ] as $slug => [$nameEn, $nameAr, $sortOrder]
        ) {
            OrderStatusType::query()->updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $this->translation($nameEn, $nameAr),
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }

    private function academyCourse(Provider $provider): Course
    {
        return Course::query()
            ->where('provider_id', $provider->id)
            ->whereHas('academyTeacher.teacher.owner', fn ($query) => $query->where('phone', '01000000002'))
            ->whereHas('accountSubject.gradeSubject', function ($query): void {
                $query
                    ->whereHas('grade', fn ($query) => $query->where('sort_order', 10))
                    ->whereHas('track', fn ($query) => $query->where('code', 'general'))
                    ->whereHas('subject', fn ($query) => $query->where('name', 'رياضيات'));
            })
            ->firstOrFail();
    }

    private function providerPaymentMethods(): void
    {
        $paymentMethods = PaymentMethod::query()->get();

        Provider::query()->each(function (Provider $provider) use ($paymentMethods): void {
            foreach ($paymentMethods as $paymentMethod) {
                if (in_array($paymentMethod->slug, [
                    PaymentMethodSlugs::Bank->value,
                    PaymentMethodSlugs::VodafoneCash->value,
                    PaymentMethodSlugs::OrangeCash->value,
                    PaymentMethodSlugs::ECash->value,
                ], true)) {
                    continue;
                }

                /** @var ProviderPaymentMethod $providerPaymentMethod */
                $providerPaymentMethod = ProviderPaymentMethod::query()->withTrashed()->firstOrNew([
                    'provider_id' => $provider->id,
                    'payment_method_id' => $paymentMethod->id,
                ]);

                $providerPaymentMethod->fill($this->providerPaymentMethodPayload($provider, $paymentMethod));
                $providerPaymentMethod->restore();
                $providerPaymentMethod->save();
            }
        });
    }

    /**
     * @return array<string, string|null>
     */
    private function providerPaymentMethodPayload(Provider $provider, PaymentMethod $paymentMethod): array
    {
        $providerName = $provider->name ?: $provider->slug;

        if ($paymentMethod->is_bank) {
            return [
                'account_number' => 'BANK-'.$provider->id.'-0001',
                'account_holder' => $providerName,
                'phone_number' => null,
                'phone_holder' => null,
            ];
        }

        if ($paymentMethod->is_code) {
            return [
                'account_number' => 'CODE-'.$provider->id,
                'account_holder' => $providerName,
                'phone_number' => null,
                'phone_holder' => null,
            ];
        }

        return [
            'account_number' => null,
            'account_holder' => null,
            'phone_number' => '01000000'.str_pad((string) $provider->id, 3, '0', STR_PAD_LEFT),
            'phone_holder' => $providerName,
        ];
    }

    private function providerPaymentMethod(Provider $provider): ProviderPaymentMethod
    {
        $paymentMethod = PaymentMethod::query()
            ->where('slug', PaymentMethodSlugs::InstaPay->value)
            ->firstOrFail();

        /** @var ProviderPaymentMethod $providerPaymentMethod */
        $providerPaymentMethod = ProviderPaymentMethod::query()->withTrashed()->firstOrNew([
            'provider_id' => $provider->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $providerPaymentMethod->fill($this->providerPaymentMethodPayload($provider, $paymentMethod));
        $providerPaymentMethod->restore();
        $providerPaymentMethod->save();

        return $providerPaymentMethod;
    }

    private function orderStatus(Order $order, string $slug, User $createdBy): void
    {
        $statusType = OrderStatusType::query()->where('slug', $slug)->firstOrFail();

        $order->statuses()->updateOrCreate([
            'order_status_type_id' => $statusType->id,
        ], [
            'is_current' => true,
            'status_at' => now(),
            'created_by_user_id' => $createdBy->id,
        ]);
    }

    private function singleCourseCart(
        Provider $provider,
        User $student,
        Course $course,
        CoursePrice $coursePrice,
        float $singleCoursePrice
    ): void {
        $cart = Cart::query()->firstOrCreate([
            'student_user_id' => $student->id,
            'provider_id' => $provider->id,
            'purchase_type' => PurchaseType::SingleCourse,
        ], [
            'subtotal' => $singleCoursePrice,
            'total' => $singleCoursePrice,
        ]);

        CartItem::query()->firstOrCreate([
            'cart_id' => $cart->id,
            'course_id' => $course->id,
        ], [
            'course_price_id' => $coursePrice->id,
            'purchase_unit_id' => $coursePrice->purchase_unit_id,
            'purchase_type' => PurchaseType::SingleCourse,
            'title' => $course->title,
            'unit_price' => $singleCoursePrice,
            'total' => $singleCoursePrice,
        ]);
    }
}
