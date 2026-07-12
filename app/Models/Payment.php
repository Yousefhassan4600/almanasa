<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'metadata' => 'array',
            'currency' => Currency::class,
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->order?->display_name ?? "Payment #{$this->getKey()}").' - '.($this->amount ?? '0').' '.($this->currency?->value ?? ''));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function proof(): HasOne
    {
        return $this->hasOne(PaymentProof::class);
    }
}
