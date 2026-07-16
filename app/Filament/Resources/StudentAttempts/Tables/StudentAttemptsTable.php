<?php

namespace App\Filament\Resources\StudentAttempts\Tables;

use App\Enums\QuestionType;
use App\Filament\Base\BaseTable;
use App\Models\AttemptStatusType;
use App\Models\StudentAttempt;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class StudentAttemptsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->searchable()
                ->sortable(),
            TextColumn::make('student.name')
                ->label('Student')
                ->sortable(),
            TextColumn::make('course.title')
                ->label('Course')
                ->sortable(),
            TextColumn::make('attemptable_type')
                ->label('Type')
                ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                ->badge()
                ->searchable()
                ->sortable(),
            TextColumn::make('attemptable_id')
                ->label('Attemptable')
                ->getStateUsing(fn (StudentAttempt $record): string => $this->attemptableName($record)),
            TextColumn::make('attempt_number')
                ->label('Attempt Number')
                ->badge()
                ->sortable(),
            TextColumn::make('examModel.model_number')
                ->label('Exam Model')
                ->badge()
                ->placeholder('-'),
            SelectColumn::make('current_status_type_id')
                ->label('Status')
                ->getStateUsing(fn (StudentAttempt $record): ?int => $record->currentStatus?->attempt_status_type_id)
                ->options(fn (): array => AttemptStatusType::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(fn (AttemptStatusType $statusType): array => [$statusType->id => $statusType->name])
                    ->all())
                ->selectablePlaceholder(false)
                ->updateStateUsing(function (StudentAttempt $record, mixed $state): mixed {
                    $this->updateCurrentStatus($record, (int) $state);

                    return $state;
                }),
            TextColumn::make('score')
                ->label('Score')
                ->getStateUsing(fn (StudentAttempt $record): HtmlString => $this->scoreState($record))
                ->html(),
            TextColumn::make('time_spent_seconds')
                ->label('Time Spent')
                ->badge()
                ->suffix(' minutes')
                ->formatStateUsing(fn (?int $state): string => $this->formatDuration($state)),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return false;
    }

    protected function extraRecordActions(): array
    {
        return [
            Action::make('answers')
                ->label('')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading(fn (StudentAttempt $record): string => 'Student Answers - '.$this->attemptableName($record))
                ->modalContent(fn (StudentAttempt $record): View => view('filament.resources.student-attempts.answers-modal', [
                    'answers' => $record->studentAnswers()
                        ->with(['question.options', 'question_option', 'student_attempt.studentAnswers'])
                        ->oldest()
                        ->get(),
                    'attempt' => $record,
                ])),
            Action::make('grade_statement_answers')
                ->label('')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->modalHeading(fn (StudentAttempt $record): string => 'Grade Statement Answers - '.$this->attemptableName($record))
                ->schema([
                    Repeater::make('answers')
                        ->label('Statement Answers')
                        ->schema([
                            Hidden::make('id'),
                            Textarea::make('question')
                                ->label('Question')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),
                            Textarea::make('answer')
                                ->label('Student Answer')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),
                            TextInput::make('max_score')
                                ->label('Max Question Score')
                                ->readOnly()
                                ->dehydrated(false),
                            TextInput::make('score')
                                ->label('Score')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(fn ($get): ?float => (float) ($get('max_score') ?? 0))
                                ->required(),
                        ])
                        ->columns(1)
                        ->reorderable(false)
                        ->addable(false)
                        ->deletable(false)
                        ->columnSpanFull(),
                ])
                ->fillForm(fn (StudentAttempt $record): array => [
                    'answers' => $this->statementAnswersForGrading($record),
                ])
                ->action(function (StudentAttempt $record, array $data): void {
                    $this->gradeStatementAnswers($record, $data['answers'] ?? []);
                })
                ->visible(fn (StudentAttempt $record): bool => $record->studentAnswers()
                    ->whereHas('question', fn ($query) => $query->where('type', QuestionType::Statement->value))
                    ->exists()),
            Action::make('logs')
                ->label('')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalHeading('Status History')
                ->modalContent(fn (StudentAttempt $record): View => view('filament.resources.student-attempts.status-logs-modal', [
                    'statusLogs' => $record->statuses()
                        ->with(['type', 'createdBy'])
                        ->latest('status_at')
                        ->latest()
                        ->get(),
                ])),
        ];
    }

    private function attemptableName(StudentAttempt $record): string
    {
        $record->loadMissing('attemptable');

        return (string) ($record->attemptable?->title ?? class_basename((string) $record->attemptable_type).' #'.$record->attemptable_id);
    }

    private function updateCurrentStatus(StudentAttempt $record, int $statusTypeId): void
    {
        if ($statusTypeId <= 0 || $record->currentStatus?->attempt_status_type_id === $statusTypeId) {
            return;
        }

        $record->statuses()->create([
            'attempt_status_type_id' => $statusTypeId,
            'created_by_user_id' => auth()->id(),
            'is_current' => true,
            'status_at' => now(),
        ]);
    }

    /**
     * @return array<int, array{id: int, question: string, answer: string, score: mixed, max_score: float|null}>
     */
    private function statementAnswersForGrading(StudentAttempt $record): array
    {
        return $record->studentAnswers()
            ->with(['question', 'student_attempt.studentAnswers'])
            ->whereHas('question', fn ($query) => $query->where('type', QuestionType::Statement->value))
            ->oldest()
            ->get()
            ->map(fn ($answer): array => [
                'id' => $answer->id,
                'question' => (string) ($answer->question?->title ?? ''),
                'answer' => (string) ($answer->answer_text ?? '-'),
                'score' => $answer->score,
                'max_score' => $answer->question_max_degree,
            ])
            ->all();
    }

    /**
     * @param  array<int, array{id?: int|string, score?: mixed, max_score?: mixed}>  $answers
     */
    private function gradeStatementAnswers(StudentAttempt $record, array $answers): void
    {
        foreach ($answers as $answerData) {
            $answer = $record->studentAnswers()
                ->whereHas('question', fn ($query) => $query->where('type', QuestionType::Statement->value))
                ->findOrFail((int) ($answerData['id'] ?? 0));
            $maxScore = $answer->question_max_degree;
            $score = (float) ($answerData['score'] ?? 0);

            if ($maxScore !== null) {
                $score = min($score, $maxScore);
            }

            $answer->update([
                'score' => $score,
                'is_correct' => $maxScore !== null ? $score >= $maxScore : $score > 0,
            ]);
        }

        $this->markAttemptGradedIfComplete($record);

        Notification::make()
            ->title('Statement answers graded')
            ->success()
            ->send();
    }

    private function markAttemptGradedIfComplete(StudentAttempt $record): void
    {
        $hasPendingManualAnswers = $record->studentAnswers()
            ->whereHas('question', fn ($query) => $query->where('type', QuestionType::Statement->value))
            ->whereNull('score')
            ->exists();

        if ($hasPendingManualAnswers) {
            return;
        }

        $gradedStatusType = AttemptStatusType::query()
            ->where('slug', 'graded')
            ->first();

        if (! $gradedStatusType) {
            return;
        }

        $record->statuses()->create([
            'attempt_status_type_id' => $gradedStatusType->id,
            'created_by_user_id' => auth()->id(),
            'is_current' => true,
            'status_at' => now(),
        ]);
    }

    private function scoreState(StudentAttempt $record): HtmlString
    {
        if ($this->hasPendingManualAnswers($record)) {
            return $this->pendingScoreState($record);
        }

        $score = (float) ($record->score ?? 0);
        $maxScore = (float) ($record->max_score ?? 0);
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : (float) ($record->percentage ?? 0);
        $barPercentage = max(0, min(100, $percentage));
        $color = $percentage < 50
            ? '#dc2626'
            : ($percentage < 80 ? '#ca8a04' : '#059669');
        $isRtl = in_array(app()->getLocale(), ['ar', 'ur'], true);
        $direction = $isRtl ? 'rtl' : 'ltr';
        $positionSide = $isRtl ? 'right' : 'left';
        $textAlign = $isRtl ? 'right' : 'left';

        return new HtmlString(
            '<div dir="'.e($direction).'" style="min-width: 220px; direction: '.e($direction).'; text-align: '.e($textAlign).';">'
                .'<div style="display: flex; justify-content: space-between; gap: 12px; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #4b5563;">'
                .'<span dir="auto">'.e(number_format($percentage, 2)).'%</span>'
                .'<span dir="auto">'.e(number_format($score, 2)).' / '.e($maxScore > 0 ? number_format($maxScore, 2) : '0.00').'</span>'
                .'</div>'
                .'<div style="position: relative; height: 18px; width: 100%; overflow: hidden; border-radius: 999px; background: #e5e7eb;">'
                .'<div style="position: absolute; top: 0; bottom: 0; '.e($positionSide).': 0; width: '.e(number_format($barPercentage, 2)).'%; border-radius: 999px; background: '.e($color).';"></div>'
                .'</div>'
                .'<div style="display: flex; justify-content: space-between; gap: 12px; margin-top: 6px; font-size: 11px; color: #6b7280;">'
                .'<span dir="auto">Score: '.e(number_format($score, 2)).'</span>'
                .'<span dir="auto">Max: '.e($maxScore > 0 ? number_format($maxScore, 2) : '0.00').'</span>'
                .'</div>'
                .'</div>'
        );
    }

    private function hasPendingManualAnswers(StudentAttempt $record): bool
    {
        return $record->studentAnswers()
            ->whereHas('question', fn ($query) => $query->where('type', QuestionType::Statement->value))
            ->whereNull('score')
            ->exists();
    }

    private function pendingScoreState(StudentAttempt $record): HtmlString
    {
        $score = $this->answerScore($record);
        $maxScore = (float) ($record->max_score ?? 0);
        $isRtl = in_array(app()->getLocale(), ['ar', 'ur'], true);
        $direction = $isRtl ? 'rtl' : 'ltr';
        $textAlign = $isRtl ? 'right' : 'left';

        return new HtmlString(
            '<div dir="'.e($direction).'" style="min-width: 220px; direction: '.e($direction).'; text-align: '.e($textAlign).';">'
                .'<div style="display: inline-flex; align-items: center; border-radius: 999px; background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; font-size: 11px; font-weight: 700; margin-bottom: 6px;">'
                .e(__('Needs Grading'))
                .'</div>'
                .'<div style="font-size: 12px; font-weight: 600; color: #4b5563;">'
                .e(__('Partial Score')).': '.e(number_format($score, 2)).' / '.e($maxScore > 0 ? number_format($maxScore, 2) : '0.00')
                .'</div>'
                .'<div style="margin-top: 6px; height: 18px; width: 100%; overflow: hidden; border-radius: 999px; background: #fef3c7; border: 1px solid #f59e0b;"></div>'
                .'</div>'
        );
    }

    private function answerScore(StudentAttempt $record): float
    {
        return (float) $record->studentAnswers()
            ->sum('score');
    }

    private function formatDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '-';
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}
