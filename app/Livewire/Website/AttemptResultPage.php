<?php

namespace App\Livewire\Website;

use App\Models\Provider;
use App\Models\StudentAttempt;
use Illuminate\Database\Eloquent\Builder;
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
            'attempt' => $this->attempt($provider),
            'assessmentType' => $this->type,
        ]);
    }

    private function attempt(Provider $provider): ?StudentAttempt
    {
        if (! $this->attemptId || ! Auth::check()) {
            return null;
        }

        return StudentAttempt::query()
            ->with([
                'attemptable',
                'course:id,provider_id,account_subject_id,academy_teacher_id,title',
                'currentStatus.type',
                'statuses.type',
                'examModel',
                'studentAnswers' => fn ($query) => $query->oldest('id'),
                'studentAnswers.question.options',
                'studentAnswers.question_option',
            ])
            ->whereKey($this->attemptId)
            ->where('student_user_id', Auth::id())
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }
}
