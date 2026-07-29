<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Catalog\LoadTeachersPage;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class TeachersPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'subject')]
    public ?int $subjectId = null;

    private LoadTeachersPage $loadTeachersPage;

    public function boot(LoadTeachersPage $loadTeachersPage): void
    {
        $this->loadTeachersPage = $loadTeachersPage;
    }

    public function mount(): void
    {
        $this->subjectId ??= request()->integer('subject') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $gradeId = Auth::user()?->studentProfile()->value('grade_id');
        $data = $this->loadTeachersPage->handle($provider, $gradeId, $this->subjectId);
        $this->subjectId = $data['selectedSubjectId'];

        return view('livewire.website.teachers-page', [
            'provider' => $provider,
            'accountSubject' => $data['accountSubject'],
            'teachers' => $data['teachers'],
            'coursesByTeacher' => $data['coursesByTeacher'],
            'isStandaloneTeacher' => $data['isStandaloneTeacher'],
        ]);
    }
}
