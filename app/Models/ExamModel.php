<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class ExamModel extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'exam_id',
        'model_number',
        'question_ids',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'exam',
    ];

    protected function casts(): array
    {
        return [
            'question_ids' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    /**
     * @return Collection<int, array{id: int, max_score: float|null}>
     */
    public function questionItems(): Collection
    {
        return collect($this->question_ids ?? [])
            ->map(function (mixed $item): ?array {
                if (is_array($item)) {
                    $id = (int) ($item['id'] ?? 0);

                    if ($id <= 0) {
                        return null;
                    }

                    return [
                        'id' => $id,
                        'max_score' => isset($item['max_score']) ? (float) $item['max_score'] : null,
                    ];
                }

                $id = (int) $item;

                if ($id <= 0) {
                    return null;
                }

                return [
                    'id' => $id,
                    'max_score' => null,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, int>
     */
    public function questionIdList(): Collection
    {
        return $this->questionItems()
            ->pluck('id')
            ->values();
    }

    public function questionMaxScore(int $questionId): ?float
    {
        return $this->questionItems()
            ->firstWhere('id', $questionId)['max_score'] ?? null;
    }

    public function updateQuestionMaxScore(int $questionId, float $maxScore): void
    {
        $items = $this->questionItems()
            ->map(fn (array $item): array => [
                'id' => $item['id'],
                'max_score' => $item['id'] === $questionId ? round($maxScore, 2) : $item['max_score'],
            ])
            ->all();

        $this->update([
            'question_ids' => $items,
        ]);
    }
}
