<?php

namespace App\Actions\StudentPortal\Layout;

use App\Enums\PurchaseType;
use App\Models\Cart;
use App\Models\Provider;

class CountCartItems
{
    public function handle(Provider $provider, ?int $studentUserId, bool $hasCompletedProfile): int
    {
        if (! $studentUserId || ! $hasCompletedProfile) {
            return 0;
        }

        return (int) (Cart::query()
            ->whereBelongsTo($provider)
            ->where('student_user_id', $studentUserId)
            ->where('purchase_type', PurchaseType::SingleCourse->value)
            ->withCount('items')
            ->latest()
            ->first()
            ?->items_count ?? 0);
    }
}
