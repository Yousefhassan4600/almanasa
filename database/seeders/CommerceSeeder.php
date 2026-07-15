<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageCourse;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\StudentEnrollment;
use App\Models\Subscription;
use App\Models\User;

class CommerceSeeder extends BaseSeeder
{
    public function run(): void
    {
        $academyProvider = Provider::query()->where('slug', 'future-stars-academy')->firstOrFail();
        $studentUser = User::query()->where('phone', '01000000004')->firstOrFail();
        $academyOwner = User::query()->where('phone', '01000000001')->firstOrFail();
        $academyCourse = Course::query()
            ->where('provider_id', $academyProvider->id)
            ->where('title->en', 'Welcome to the Mathematics Course')
            ->firstOrFail();

        $package = Package::query()->firstOrCreate([
            'provider_id' => $academyProvider->id,
            'duration_days' => 120,
        ], [
            'name' => $this->translation(
                'Secondary 1 Mathematics Term Package',
                'باقة رياضيات الصف الأول الثانوي للترم'
            ),
            'description' => $this->translation(
                'Access to the full first-term mathematics course.',
                'وصول كامل إلى كورس الرياضيات للترم الأول.'
            ),
            'price' => 500,
            'is_featured' => true,
            'status' => ContentStatus::Published,
        ]);

        PackageCourse::query()->firstOrCreate([
            'package_id' => $package->id,
            'course_id' => $academyCourse->id,
        ]);

        $subscription = Subscription::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addDays(120),
            'auto_renew' => false,
        ]);

        StudentEnrollment::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'course_id' => $academyCourse->id,
        ], [
            'package_id' => $package->id,
            'subscription_id' => $subscription->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now(),
            'expires_at' => now()->addDays(120),
        ]);

        $cart = Cart::query()->firstOrCreate([
            'student_user_id' => $studentUser->id,
            'provider_id' => $academyProvider->id,
            'status' => 'converted',
        ], [
            'subtotal' => 500,
            'tax' => 0,
            'discount' => 0,
            'total' => 500,
        ]);

        CartItem::query()->firstOrCreate([
            'cart_id' => $cart->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'quantity' => 1,
            'unit_price' => 500,
            'total' => 500,
        ]);

        $order = Order::query()->firstOrCreate([
            'order_number' => 'ORD-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'cart_id' => $cart->id,
            'subtotal' => 500,
            'tax' => 0,
            'discount' => 0,
            'total' => 500,
            'status' => PaymentStatus::Paid,
        ]);

        OrderItem::query()->firstOrCreate([
            'order_id' => $order->id,
            'package_id' => $package->id,
        ], [
            'course_id' => $academyCourse->id,
            'subscription_id' => $subscription->id,
            'title' => $package->name,
            'unit_price' => 500,
            'quantity' => 1,
            'total' => 500,
        ]);

        Payment::query()->firstOrCreate([
            'order_id' => $order->id,
            'transaction_reference' => 'PAY-ALM-0001',
        ], [
            'provider_id' => $academyProvider->id,
            'student_user_id' => $studentUser->id,
            'method' => PaymentMethod::Instapay,
            'status' => PaymentStatus::Paid,
            'amount' => 500,
            'paid_at' => now(),
            'reviewed_by_user_id' => $academyOwner->id,
            'reviewed_at' => now(),
        ]);
    }
}
