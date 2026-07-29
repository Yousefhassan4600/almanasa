<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Catalog\ListAccountSubjects;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SubjectsPage extends Component
{
    #[Locked]
    public int $providerId;

    public string $search = '';

    private ListAccountSubjects $listAccountSubjects;

    public function boot(ListAccountSubjects $listAccountSubjects): void
    {
        $this->listAccountSubjects = $listAccountSubjects;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $profile = Auth::user()?->studentProfile()
            ->with(['grade:id,name,education_stage_id', 'grade.educationStage:id,name'])
            ->first();

        $gradeId = $profile?->grade_id;

        return view('livewire.website.subjects-page', [
            'provider' => $provider,
            'subjects' => $this->listAccountSubjects->handle($provider, $gradeId, $this->search, withActiveTeachersCount: true),
            'gradeName' => $profile?->grade?->name,
            'stageName' => $profile?->grade?->educationStage?->name,
            'hasGradeFilter' => filled($gradeId),
        ]);
    }
}
