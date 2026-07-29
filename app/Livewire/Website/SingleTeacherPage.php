<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Catalog\LoadSingleTeacherPage;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class SingleTeacherPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'teacher')]
    public ?int $teacherId = null;

    #[Url(as: 'subject')]
    public ?int $subjectId = null;

    private LoadSingleTeacherPage $loadSingleTeacherPage;

    public function boot(LoadSingleTeacherPage $loadSingleTeacherPage): void
    {
        $this->loadSingleTeacherPage = $loadSingleTeacherPage;
    }

    public function mount(): void
    {
        $this->teacherId ??= request()->integer('teacher') ?: null;
        $this->subjectId ??= request()->integer('subject') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $gradeId = Auth::user()?->studentProfile()->value('grade_id');
        $data = $this->loadSingleTeacherPage->handle($provider, $this->teacherId, $this->subjectId, Auth::id(), $gradeId);
        $this->subjectId = $data['selectedSubjectId'];

        return view('livewire.website.single-teacher-page', [
            'provider' => $provider,
            'teacher' => $data['teacher'],
            'accountSubject' => $data['accountSubject'],
            'course' => $data['course'],
            'hasCourseSubscription' => $data['hasCourseSubscription'],
            'monthlyPrice' => $data['monthlyPrice'],
        ]);
    }
}
