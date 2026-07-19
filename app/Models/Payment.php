<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'order_id',
        'provider_id',
        'student_user_id',
        'provider_payment_method_id',
        'amount',
        'transaction_reference',
        'provider_code_id',
        'sender_phone',
        'transfer_image',
        'gateway_response',
        'paid_at',
        'reviewed_by_user_id',
        'reviewed_at',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function providerPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(ProviderPaymentMethod::class, 'provider_payment_method_id');
    }

    public function providerCode(): BelongsTo
    {
        return $this->belongsTo(ProviderCode::class, 'provider_code_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
