<?php

namespace App\Actions\StudentPortal\Codes;

use App\Enums\PurchaseType;
use App\Enums\PurchaseUnitType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ProviderCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RedeemProviderCode
{
    public function handle(Provider $provider, User $student, string $value): void
    {
        DB::transaction(function () use ($provider, $student, $value): void {
            $code = ProviderCode::query()
                ->whereBelongsTo($provider)
                ->where('code', trim($value))
                ->lockForUpdate()
                ->first();

            if (! $code) {
                throw ValidationException::withMessages(['code' => 'الكود غير صحيح.']);
            }

            $code->loadMissing('course', 'lesson', 'purchaseUnit');

            if (! $code->course || ! $code->purchaseUnit ||
                ($code->purchaseUnit->type === PurchaseUnitType::Lesson && (! $code->lesson || (int) $code->lesson->course_id !== (int) $code->course_id))) {
                throw ValidationException::withMessages(['code' => 'الكود غير مرتبط بكورس أو حصة صالحة.']);
            }

            $order = Order::query()->create([
                'provider_id' => $provider->id,
                'student_user_id' => $student->id,
                'order_number' => 'CODE-'.$provider->id.'-'.Str::upper(Str::random(16)),
                'purchase_type' => PurchaseType::SingleCourse->value,
                'subtotal' => 0,
                'total' => 0,
            ]);

            $order->items()->create([
                'course_id' => $code->course_id,
                'purchase_unit_id' => $code->purchase_unit_id,
                'purchase_type' => PurchaseType::SingleCourse->value,
                'title' => $code->course->title,
                'unit_price' => 0,
                'total' => 0,
            ]);

            Payment::query()->create([
                'order_id' => $order->id,
                'provider_id' => $provider->id,
                'student_user_id' => $student->id,
                'provider_code_id' => $code->id,
            ]);
        });
    }
}
