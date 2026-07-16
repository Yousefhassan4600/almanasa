<div style="padding: 0.5rem;">
    <div style="display: grid; gap: 0.75rem;">
        @forelse($answers as $answer)
            @php
                $correctAnswer = $answer->correct_answer;
                $questionMaxDegree = $answer->question_max_degree;
                $requiresManualGrading = $answer->requires_manual_grading;
                $scoreLabel = $answer->score !== null ? number_format((float) $answer->score, 2) : ($requiresManualGrading ? __('Pending') : '-');
            @endphp

            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                            {{ __('Question') }} #{{ $loop->iteration }}
                        </p>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #111827;">
                            {{ $answer->question?->title ?? __('Unknown Question') }}
                        </h4>
                    </div>

                    @if($requiresManualGrading && $answer->score === null)
                        <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #fef3c7; color: #92400e; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                            {{ __('Needs Grading') }}
                        </span>
                    @elseif($answer->is_correct === null)
                        <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #f3f4f6; color: #374151; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                            {{ __('Pending') }}
                        </span>
                    @elseif($answer->is_correct)
                        <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #dcfce7; color: #166534; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                            {{ __('Correct') }}
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #fee2e2; color: #991b1b; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                            {{ __('Wrong') }}
                        </span>
                    @endif
                </div>

                <div style="display: grid; gap: 0.4rem; color: #374151; font-size: 0.85rem;">
                    <div>
                        <span style="font-weight: 700;">{{ __('Answer') }}:</span>
                        <span>{{ $answer->question_option?->title ?? $answer->answer_text ?? '-' }}</span>
                    </div>
                    @if(! $requiresManualGrading && $answer->is_correct === false && filled($correctAnswer))
                        <div>
                            <span style="font-weight: 700;">{{ __('Correct Answer') }}:</span>
                            <span>{{ $correctAnswer }}</span>
                        </div>
                    @endif
                    <div>
                        <span style="font-weight: 700;">{{ __('Score') }}:</span>
                        <span>
                            {{ $scoreLabel }}
                            /
                            {{ $questionMaxDegree !== null ? number_format((float) $questionMaxDegree, 2) : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div style="border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; color: #64748b; text-align: center;">
                {{ __('No answers found for this attempt.') }}
            </div>
        @endforelse
    </div>
</div>
