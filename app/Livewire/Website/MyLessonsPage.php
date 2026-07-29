<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Students\LoadMyLessons;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MyLessonsPage extends Component
{
    #[Locked]
    public int $providerId;

    private LoadMyLessons $loadMyLessons;

    public function boot(LoadMyLessons $loadMyLessons): void
    {
        $this->loadMyLessons = $loadMyLessons;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $data = $this->loadMyLessons->handle($provider, Auth::user());

        return view('livewire.website.my-lessons-page', [
            'provider' => $provider,
            ...$data,
        ]);
    }
}
