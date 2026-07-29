<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Catalog\ListAccountSubjects;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class HomeSubjects extends Component
{
    #[Locked]
    public int $providerId;

    private ListAccountSubjects $listAccountSubjects;

    public function boot(ListAccountSubjects $listAccountSubjects): void
    {
        $this->listAccountSubjects = $listAccountSubjects;
    }

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
            'subjects' => $this->listAccountSubjects->handle($provider, $gradeId, limit: 7),
            'hasGradeFilter' => filled($gradeId),
            'isAuthenticated' => true,
        ]);
    }
}
