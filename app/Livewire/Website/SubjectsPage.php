<?php

namespace App\Livewire\Website;

use App\Models\AccountSubject;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SubjectsPage extends Component
{
    #[Locked]
    public int $providerId;

    public string $search = '';

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $profile = Auth::user()?->studentProfile()
            ->with(['grade:id,name,education_stage_id', 'grade.educationStage:id,name'])
            ->first();

        $gradeId = $profile?->grade_id;

        return view('livewire.website.subjects-page', [
            'provider' => $provider,
            'subjects' => $this->subjects($provider, $gradeId),
            'gradeName' => $profile?->grade?->name,
            'stageName' => $profile?->grade?->educationStage?->name,
            'hasGradeFilter' => filled($gradeId),
        ]);
    }

    /**
     * @return Collection<int, AccountSubject>
     */
    private function subjects(Provider $provider, ?int $gradeId): Collection
    {
        $search = trim($this->search);

        return AccountSubject::query()
            ->with([
                'gradeSubject:id,grade_id,subject_id',
                'gradeSubject.subject:id,track_id,name,icon,description',
                'gradeSubject.subject.track:id,name',
            ])
            ->withCount([
                'teacherAssignments as active_teachers_count' => fn ($query) => $query->where('is_active', true),
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
            ->whereHas('gradeSubject.subject')
            ->get();
    }
}
