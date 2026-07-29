<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Assessments\ManageAssessmentAttempt;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class AssessmentPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Locked]
    public string $type = 'assignment';

    #[Url(as: 'assignment')]
    public ?int $assignmentId = null;

    #[Url(as: 'exam')]
    public ?int $examId = null;

    #[Url(as: 'item')]
    public ?int $itemId = null;

    #[Url(as: 'retry')]
    public bool $retry = false;

    /**
     * @var array<int|string, mixed>
     */
    public array $answers = [];

    public int $currentQuestionIndex = 0;

    private ManageAssessmentAttempt $manageAssessmentAttempt;

    public function boot(ManageAssessmentAttempt $manageAssessmentAttempt): void
    {
        $this->manageAssessmentAttempt = $manageAssessmentAttempt;
    }

    public function mount(int $providerId, string $type = 'assignment'): void
    {
        $this->providerId = $providerId;
        $this->type = $type === 'exam' ? 'exam' : 'assignment';
        $this->assignmentId ??= request()->integer('assignment') ?: null;
        $this->examId ??= request()->integer('exam') ?: null;
        $this->itemId ??= request()->integer('item') ?: null;
        $this->retry = request()->boolean('retry', $this->retry);

        $this->manageAssessmentAttempt->start(
            $this->providerId,
            $this->type,
            $this->assignmentId,
            $this->examId,
            $this->itemId,
            $this->retry,
            Auth::id(),
        );
    }

    public function nextQuestion(): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $questionsCount = $this->manageAssessmentAttempt->questionsCount(
            $provider,
            $this->type,
            $this->assignmentId,
            $this->examId,
            Auth::id(),
        );

        if ($questionsCount === 0) {
            $this->currentQuestionIndex = 0;

            return;
        }

        $this->currentQuestionIndex = min($questionsCount - 1, $this->currentQuestionIndex + 1);
    }

    public function previousQuestion(): void
    {
        $this->currentQuestionIndex = max(0, $this->currentQuestionIndex - 1);
    }

    public function submit(bool $force = false): mixed
    {
        $this->resetErrorBag();

        if (! Auth::check()) {
            return redirect('/login');
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $result = $this->manageAssessmentAttempt->submit(
            $provider,
            $this->type,
            $this->assignmentId,
            $this->examId,
            $this->itemId,
            Auth::id(),
            $this->answers,
            $force,
        );

        foreach ($result['errors'] as $key => $message) {
            $this->addError($key, $message);
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return null;
        }

        if ($result['redirectAttempt']) {
            return redirect($this->manageAssessmentAttempt->resultUrl($this->type, $result['redirectAttempt']));
        }

        if (! $result['attempt']) {
            return null;
        }

        return redirect($this->manageAssessmentAttempt->resultUrl($this->type, $result['attempt']));
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $data = $this->manageAssessmentAttempt->renderData(
            $provider,
            $this->type,
            $this->assignmentId,
            $this->examId,
            $this->itemId,
            $this->retry,
            $this->currentQuestionIndex,
            Auth::id(),
        );

        $questionsCount = $data['questions']->count();
        $this->currentQuestionIndex = max(0, min($this->currentQuestionIndex, max(0, $questionsCount - 1)));

        return view('livewire.website.assessment-page', [
            'provider' => $provider,
            ...$data,
        ]);
    }
}
