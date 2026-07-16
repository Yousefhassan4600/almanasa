<?php

namespace App\Actions\Questions;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use Throwable;

class ImportQuestionsFromSpreadsheet
{
    /**
     * @return array{imported: int, skipped: int}
     *
     * @throws Throwable
     */
    public function handle(string $path, ?int $lessonId = null, ?int $courseId = null): array
    {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'numbers') {
            throw ValidationException::withMessages([
                'file' => 'Apple Numbers files are not supported by the server importer. Export the file from Numbers as Excel (.xlsx) or CSV, then upload it.',
            ]);
        }

        $reader = ReaderFactory::createFromFile($path);
        $reader->open($path);

        $headers = [];
        $imported = 0;
        $skipped = 0;

        try {
            DB::transaction(function () use ($reader, $lessonId, $courseId, &$headers, &$imported, &$skipped): void {
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                        $values = $this->rowValues($row->getCells());

                        if ($rowIndex === 1) {
                            $headers = $this->headers($values);

                            continue;
                        }

                        $data = $this->rowData($headers, $values);

                        if (blank($data['question_title'] ?? null)) {
                            $skipped++;

                            continue;
                        }

                        $resolvedLessonId = $lessonId ?? $this->integerValue($data['lesson_id'] ?? null);

                        if (! $resolvedLessonId) {
                            throw ValidationException::withMessages([
                                'file' => "Row {$rowIndex}: lesson_id is required.",
                            ]);
                        }

                        $lesson = Lesson::query()
                            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
                            ->find($resolvedLessonId);

                        if (! $lesson) {
                            throw ValidationException::withMessages([
                                'file' => "Row {$rowIndex}: lesson_id {$resolvedLessonId} does not belong to this scope.",
                            ]);
                        }

                        $options = $this->options($data);
                        $questionType = $this->questionType($options, $rowIndex);
                        $difficulty = $this->difficulty($data['difficulty'] ?? null, $rowIndex);

                        $question = Question::query()->create([
                            'lesson_id' => $lesson->id,
                            'title' => $this->stringValue($data['question_title']),
                            'type' => $questionType,
                            'difficulty' => $difficulty,
                        ]);

                        foreach ($options as $optionIndex => $optionTitle) {
                            $question->options()->create([
                                'title' => $optionTitle,
                                'is_correct' => $optionIndex === 0,
                                'sort_order' => $optionIndex + 1,
                            ]);
                        }

                        $imported++;
                    }

                    break;
                }
            });
        } finally {
            $reader->close();
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<int, Cell>  $cells
     * @return array<int, mixed>
     */
    private function rowValues(array $cells): array
    {
        return array_map(
            static fn ($cell): mixed => $cell->getValue(),
            $cells,
        );
    }

    /**
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    private function headers(array $values): array
    {
        return array_map(
            fn (mixed $value): string => str($this->stringValue($value))->lower()->replace([' ', '-'], '_')->toString(),
            $values,
        );
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, mixed>  $values
     * @return array<string, mixed>
     */
    private function rowData(array $headers, array $values): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            if (blank($header)) {
                continue;
            }

            $data[$header] = $values[$index] ?? null;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    private function options(array $data): array
    {
        return collect([1, 2, 3, 4])
            ->map(fn (int $index): string => $this->stringValue($data["option_{$index}"] ?? null))
            ->filter(fn (string $value): bool => filled($value))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $options
     */
    private function questionType(array $options, int $rowIndex): QuestionType
    {
        return match (count($options)) {
            0 => QuestionType::Statement,
            2 => QuestionType::TrueFalse,
            3, 4 => QuestionType::Mcq,
            default => throw ValidationException::withMessages([
                'file' => "Row {$rowIndex}: options count must be 0, 2, 3, or 4.",
            ]),
        };
    }

    private function difficulty(mixed $value, int $rowIndex): QuestionDifficulty
    {
        return match ($this->integerValue($value)) {
            1 => QuestionDifficulty::Easy,
            2 => QuestionDifficulty::Medium,
            3 => QuestionDifficulty::Hard,
            default => throw ValidationException::withMessages([
                'file' => "Row {$rowIndex}: difficulty must be 1, 2, or 3.",
            ]),
        };
    }

    private function integerValue(mixed $value): ?int
    {
        if (blank($value)) {
            return null;
        }

        return (int) $value;
    }

    private function stringValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'True' : 'False';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return trim((string) $value);
    }
}
