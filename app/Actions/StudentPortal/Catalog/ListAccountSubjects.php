<?php

namespace App\Actions\StudentPortal\Catalog;

use App\Models\AccountSubject;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;

class ListAccountSubjects
{
    /**
     * @return Collection<int, AccountSubject>
     */
    public function handle(Provider $provider, ?int $gradeId, ?string $search = null, ?int $limit = null, bool $withActiveTeachersCount = false): Collection
    {
        $search = trim((string) $search);

        $query = AccountSubject::query()
            ->with([
                'gradeSubject:id,grade_id,track_id,subject_id',
                'gradeSubject.track:id,name',
                'gradeSubject.subject:id,name,icon,description',
            ])
            ->whereBelongsTo($provider)
            ->where('is_active', true)
            ->when(
                $gradeId,
                fn ($query) => $query->whereHas(
                    'gradeSubject',
                    fn ($query) => $query->where('grade_id', $gradeId),
                ),
            )
            ->when(
                $search !== '',
                fn ($query) => $query->whereHas(
                    'gradeSubject.subject',
                    fn ($query) => $query->where('name', 'like', '%'.$search.'%'),
                ),
            )
            ->whereHas('gradeSubject.subject');

        if ($withActiveTeachersCount) {
            $query->withCount([
                'teacherAssignments as active_teachers_count' => fn ($query) => $query->where('is_active', true),
            ]);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
