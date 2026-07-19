<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ProviderCode extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'course_id',
        'lesson_id',
        'code',
        'purchase_unit_id',
        'expiry_date',
        'num_of_uses',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(PurchaseUnit::class, 'purchase_unit_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'provider_code_id');
    }

    public function assertValidForPayment(Payment $payment): void
    {
        $payment->loadMissing('order.items', 'order.provider');
        $this->loadMissing('lesson');

        $order = $payment->order;

        if (! $order) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('Payment must belong to an order before using a provider code.'),
            ]);
        }

        if ((int) $this->provider_id !== (int) $order->provider_id || (int) $this->provider_id !== (int) $payment->provider_id) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code does not belong to the selected provider.'),
            ]);
        }

        if ((int) $order->student_user_id !== (int) $payment->student_user_id) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('Payment student does not match the order student.'),
            ]);
        }

        if ($this->expiry_date instanceof Carbon && $this->expiry_date->lt(today())) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code has expired.'),
            ]);
        }

        if (! $order->items->count()) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('Order must have items before using a provider code.'),
            ]);
        }

        $this->assertUsageIsAvailable($payment);
        $this->assertMatchesOrderItems($order->items);
    }

    private function assertUsageIsAvailable(Payment $payment): void
    {
        $paidUses = $this->payments()
            ->where('is_paid', true)
            ->when($payment->exists, fn ($query) => $query->whereKeyNot($payment->getKey()))
            ->count();

        if ($paidUses >= $this->num_of_uses) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code has reached its usage limit.'),
            ]);
        }
    }

    /**
     * @param  Collection<int, OrderItem>  $orderItems
     */
    private function assertMatchesOrderItems(Collection $orderItems): void
    {
        if ($orderItems->contains(fn (OrderItem $item): bool => (int) $item->purchase_unit_id !== (int) $this->purchase_unit_id)) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code is not valid for the selected purchase unit.'),
            ]);
        }

        if ($this->course_id && $orderItems->contains(fn (OrderItem $item): bool => (int) $item->course_id !== (int) $this->course_id)) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code is not valid for the selected course.'),
            ]);
        }

        if (! $this->lesson_id) {
            return;
        }

        if (! $this->lesson || $this->course_id && (int) $this->lesson->course_id !== (int) $this->course_id) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code is linked to invalid lesson data.'),
            ]);
        }

        if ($orderItems->contains(fn (OrderItem $item): bool => (int) $item->course_id !== (int) $this->lesson->course_id)) {
            throw ValidationException::withMessages([
                'provider_code_id' => __('This code is not valid for the selected lesson.'),
            ]);
        }
    }
}
