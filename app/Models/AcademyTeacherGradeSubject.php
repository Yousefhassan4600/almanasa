<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class AcademyTeacherGradeSubject extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected array $tenantRelations = [
        'academyTeacher',
        'accountSubject',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (AcademyTeacherGradeSubject $assignment): void {
            $academyTeacher = AcademyTeacher::query()->find($assignment->academy_teacher_id);
            $accountSubject = AccountSubject::query()->find($assignment->account_subject_id);

            if (
                $academyTeacher
                && $accountSubject
                && $academyTeacher->provider_id !== $accountSubject->provider_id
            ) {
                throw ValidationException::withMessages([
                    'account_subject_id' => 'The selected grade subject must belong to the same provider.',
                ]);
            }
        });
    }

    public function academyTeacher(): BelongsTo
    {
        return $this->belongsTo(AcademyTeacher::class);
    }

    public function accountSubject(): BelongsTo
    {
        return $this->belongsTo(AccountSubject::class);
    }
}
