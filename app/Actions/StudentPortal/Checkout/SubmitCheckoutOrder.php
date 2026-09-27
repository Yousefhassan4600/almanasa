<?php

namespace App\Actions\StudentPortal\Checkout;

use App\Enums\PurchaseType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderStatusType;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ProviderCode;
use App\Models\ProviderPaymentMethod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SubmitCheckoutOrder
{
    public function handle(
        Provider $provider,
        int $studentUserId,
        ProviderPaymentMethod $paymentMethod,
        Cart $cart,
        ?TemporaryUploadedFile $transferImage = null,
        ?string $transactionReference = null,
        ?string $providerCode = null,
    ): string {
        $cart->loadMissing('items');

        return DB::transaction(function () use ($provider, $studentUserId, $paymentMethod, $cart, $transferImage, $transactionReference, $providerCode): string {
            $isCodePayment = (bool) $paymentMethod->paymentMethod?->is_code;
            $code = null;

            if ($isCodePayment) {
                if (blank($providerCode)) {
                    throw ValidationException::withMessages(['providerCode' => 'يرجى إدخال الكود.']);
                }

                $code = ProviderCode::query()
                    ->whereBelongsTo($provider)
                    ->where('code', trim((string) $providerCode))
                    ->lockForUpdate()
                    ->first();

                if (! $code) {
                    throw ValidationException::withMessages(['providerCode' => 'الكود غير صحيح.']);
                }
            }

            $transferImagePath = $isCodePayment ? null : $this->storeTransferImage($transferImage);
            $order = Order::query()->create([
                'provider_id' => $provider->id,
                'student_user_id' => $studentUserId,
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
                'order_status_type_id' => ($isCodePayment ? $this->paidStatusType() : $this->pendingStatusType())->id,
                'is_current' => true,
                'status_at' => now(),
                'created_by_user_id' => $studentUserId,
                'notes' => $isCodePayment ? 'Paid with provider code.' : 'Waiting for provider approval.',
            ]);

            Payment::query()->create([
                'order_id' => $order->id,
                'provider_id' => $provider->id,
                'student_user_id' => $studentUserId,
                'provider_payment_method_id' => $paymentMethod->id,
                'provider_code_id' => $code?->id,
                'transaction_reference' => $isCodePayment ? null : $transactionReference,
                'transfer_image' => $transferImagePath,
                'is_paid' => false,
            ]);

            return $order->order_number;
        });
    }

    private function storeTransferImage(TemporaryUploadedFile|UploadedFile|null $transferImage): ?string
    {
        return $transferImage?->store('payment-proofs', 'public');
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

    private function paidStatusType(): OrderStatusType
    {
        return OrderStatusType::query()->firstOrCreate([
            'slug' => 'paid',
        ], [
            'name' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'sort_order' => 2,
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
