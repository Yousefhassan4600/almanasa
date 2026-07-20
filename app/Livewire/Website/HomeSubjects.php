<?php

namespace App\Livewire\Website;

use App\Models\AccountSubject;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class HomeSubjects extends Component
{
    #[Locked]
    public int $providerId;

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        if (! Auth::check()) {
            return view('livewire.website.home-subjects', [
                'provider' => $provider,
                'subjects' => new Collection,
                'hasGradeFilter' => false,
                'isAuthenticated' => false,
            ]);
        }

        $gradeId = Auth::user()?->studentProfile()->value('grade_id');

        return view('livewire.website.home-subjects', [
            'provider' => $provider,
            'subjects' => $this->subjects($provider, $gradeId),
            'hasGradeFilter' => filled($gradeId),
            'isAuthenticated' => true,
        ]);
    }

    /**
     * @return Collection<int, AccountSubject>
     */
    private function subjects(Provider $provider, ?int $gradeId): Collection
    {
        return AccountSubject::query()
            ->with([
                'gradeSubject:id,grade_id,subject_id',
                'gradeSubject.subject:id,track_id,name,icon',
                'gradeSubject.subject.track:id,name',
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
            ->whereHas('gradeSubject.subject')
            ->limit(7)
            ->get();
    }
}
