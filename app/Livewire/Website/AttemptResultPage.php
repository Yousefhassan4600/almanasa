<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Attempts\LoadAttemptResult;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class AttemptResultPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Locked]
    public string $type = 'assignment';

    #[Url(as: 'attempt')]
    public ?int $attemptId = null;

    #[Locked]
    public bool $showReview = false;

    #[Url(as: 'review')]
    public bool $review = false;

    private LoadAttemptResult $loadAttemptResult;

    public function boot(LoadAttemptResult $loadAttemptResult): void
    {
        $this->loadAttemptResult = $loadAttemptResult;
    }

    public function mount(int $providerId, string $type = 'assignment', bool $showReview = false): void
    {
        $this->providerId = $providerId;
        $this->type = $type === 'exam' ? 'exam' : 'assignment';
        $this->review = request()->boolean('review', $this->review);
        $this->showReview = $showReview || $this->review;
        $this->attemptId ??= request()->integer('attempt') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        return view('livewire.website.attempt-result-page', [
            'provider' => $provider,
            'attempt' => $this->loadAttemptResult->handle($provider, Auth::id(), $this->attemptId),
            'assessmentType' => $this->type,
        ]);
    }
}
