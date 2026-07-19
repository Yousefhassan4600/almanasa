<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\AccountType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'type',
        'owner_user_id',
        'is_active',
        'approved_at',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'provider_id', 'provider_id');
    }

    public function accountSubjects(): HasMany
    {
        return $this->hasMany(AccountSubject::class, 'provider_id', 'provider_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'provider_id', 'provider_id');
    }

    public function academyTeacherAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'teacher_account_id');
    }

    public function academyTeachers(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'provider_id', 'provider_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'user_id', 'owner_user_id');
    }

    public function parentStudents(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'student_user_id', 'owner_user_id');
    }

    public function studentAttempts(): HasMany
    {
        return $this->hasMany(StudentAttempt::class, 'student_user_id', 'owner_user_id')
            ->whereHas('course', fn ($query) => $query->where('provider_id', $this->provider_id));
    }

    public function lessonProgresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'student_user_id', 'owner_user_id')
            ->whereHas('course', fn ($query) => $query->where('provider_id', $this->provider_id));
    }

    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'student_user_id', 'owner_user_id')
            ->whereHas('course', fn ($query) => $query->where('provider_id', $this->provider_id));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'student_user_id', 'owner_user_id')
            ->where('provider_id', $this->provider_id);
    }

    public function canAccessDashboard(): bool
    {
        return $this->is_active
            && $this->type->canAccessDashboard();
    }

    public function canAccessWebsite(): bool
    {
        return $this->is_active
            && $this->type->canAccessWebsite();
    }

    public function canCreateSubAccounts(): bool
    {
        return $this->is_active
            && $this->type->canCreateSubAccounts();
    }
}
