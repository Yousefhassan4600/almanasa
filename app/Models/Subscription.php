<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PurchaseType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'student_user_id',
        'provider_id',
        'course_id',
        'lesson_id',
        'order_item_id',
        'purchase_unit_id',
        'purchase_type',
        'starts_at',
        'ends_at',
        'deleted_by',
    ];

    protected $attributes = [
        'purchase_type' => 'single_course',
    ];

    protected $appends = [
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_type' => PurchaseType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected function isActive(): Attribute
    {
        return Attribute::get(fn (): bool => (! $this->starts_at || $this->starts_at->lte(now()))
            && (! $this->ends_at || $this->ends_at->gte(now())));
    }

    public function scopeActiveForStudentCourse(Builder $query, int $studentUserId, Course $course): Builder
    {
        return $query
            ->where('student_user_id', $studentUserId)
            ->where('provider_id', $course->provider_id)
            ->where('course_id', $course->id)
            ->whereNull('lesson_id')
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now()));
    }

    public function scopeActiveForStudentLesson(Builder $query, int $studentUserId, Lesson $lesson): Builder
    {
        return $query
            ->where('student_user_id', $studentUserId)
            ->where('provider_id', $lesson->course->provider_id)
            ->where('course_id', $lesson->course_id)
            ->where(fn (Builder $query): Builder => $query->whereNull('lesson_id')->orWhere('lesson_id', $lesson->id))
            ->where(fn (Builder $query): Builder => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query): Builder => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeActiveForSpecificLesson(Builder $query, int $studentUserId, Lesson $lesson): Builder
    {
        return $query->activeForStudentLesson($studentUserId, $lesson)->where('lesson_id', $lesson->id);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(PurchaseUnit::class, 'purchase_unit_id');
    }
}
