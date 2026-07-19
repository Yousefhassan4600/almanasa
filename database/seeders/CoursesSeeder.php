<?php

namespace Database\Seeders;

use App\Enums\CoursePeriodType;
use App\Enums\LessonTypeEnum;
use App\Enums\PurchaseUnitType;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\AcademyTeacher;
use App\Models\AccountSubject;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\CoursePeriod;
use App\Models\CoursePrice;
use App\Models\Exam;
use App\Models\ExamModel;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\ProviderCode;
use App\Models\PurchaseUnit;
use App\Models\Question;
use App\Models\QuestionOption;

class CoursesSeeder extends BaseSeeder
{
    public function run(): void
    {
        $academyProvider = Provider::query()->where('slug', 'future-stars-academy')->firstOrFail();
        $academyMathCoverage = AccountSubject::query()
            ->where('provider_id', $academyProvider->id)
            ->whereHas('gradeSubject.subject', fn ($query) => $query->where('name->en', 'Mathematics'))
            ->firstOrFail();

        $academyTeacherAssignment = AcademyTeacher::query()
            ->where('provider_id', $academyProvider->id)
            ->whereHas('teacher.owner', fn ($query) => $query->where('phone', '01000000002'))
            ->firstOrFail();

        $lessonPurchaseUnit = PurchaseUnit::query()->where('type', PurchaseUnitType::Lesson->value)->firstOrFail();
        $monthPurchaseUnit = PurchaseUnit::query()->where('type', PurchaseUnitType::Month->value)->firstOrFail();
        $termOnePeriod = CoursePeriod::query()->where('type', CoursePeriodType::Term1->value)->firstOrFail();

        $academyCourse = Course::query()->updateOrCreate([
            'provider_id' => $academyProvider->id,
            'account_subject_id' => $academyMathCoverage->id,
            'academy_teacher_id' => $academyTeacherAssignment->id,
        ], [
            'title' => $this->translation(
                'Welcome to the Mathematics Course',
                'أهلاً بك في كورس الرياضيات'
            ),
            'description' => $this->translation(
                'Secondary 1 scientific mathematics course with recorded lessons, practice, assignments, and exams.',
                'كورس رياضيات للصف الأول الثانوي علمي رياضة يحتوي على دروس مسجلة وتدريبات وواجبات وامتحانات.'
            ),
            'weekly_lectures_count' => 2,
            'num_of_lessons' => 65,
            'num_of_hours' => 12,
            'academy_percentage' => 50,
            'teacher_percentage' => 40,
            'platform_percentage' => 10,
        ]);

        $this->coursePrices($academyCourse, $lessonPurchaseUnit, $monthPurchaseUnit);
        $this->providerCode($academyProvider, $academyCourse, $monthPurchaseUnit);
        $this->courseOutcomes($academyCourse);
        $this->lessonContent($academyCourse, $termOnePeriod);
    }

    private function coursePrices(Course $course, PurchaseUnit $lessonPurchaseUnit, PurchaseUnit $monthPurchaseUnit): void
    {
        CoursePrice::query()->updateOrCreate([
            'course_id' => $course->id,
            'purchase_unit_id' => $lessonPurchaseUnit->id,
        ], [
            'price' => 80,
            'offer_price' => null,
        ]);

        CoursePrice::query()->updateOrCreate([
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
        ], [
            'price' => 500,
            'offer_price' => 450,
        ]);
    }

    private function providerCode(Provider $provider, Course $course, PurchaseUnit $monthPurchaseUnit): void
    {
        ProviderCode::query()->updateOrCreate([
            'provider_id' => $provider->id,
            'code' => 'FUTURE-STARS-MONTH',
        ], [
            'course_id' => $course->id,
            'purchase_unit_id' => $monthPurchaseUnit->id,
            'expiry_date' => now()->addMonth()->toDateString(),
            'num_of_uses' => 100,
        ]);
    }

    private function courseOutcomes(Course $course): void
    {
        foreach (
            [
                1 => [
                    'en' => 'Master all required mathematics lessons.',
                    'ar' => 'إتقان جميع دروس منهج الرياضيات المقررة',
                ],
                2 => [
                    'en' => 'Solve different types of questions and practice exercises.',
                    'ar' => 'حل جميع أنواع المسائل والتدريبات المتنوعة',
                ],
                3 => [
                    'en' => 'Understand rules and concepts deeply and easily.',
                    'ar' => 'فهم القوانين والمفاهيم بعمق وسهولة',
                ],
                4 => [
                    'en' => 'Practice intensively on exam questions.',
                    'ar' => 'التدريب المكثف على أسئلة الامتحانات',
                ],
                5 => [
                    'en' => 'Achieve the highest grades, God willing.',
                    'ar' => 'تحقيق أعلى الدرجات بإذن الله',
                ],
            ] as $sortOrder => $outcome
        ) {
            CourseOutcome::query()->updateOrCreate([
                'course_id' => $course->id,
                'sort_order' => $sortOrder,
            ], [
                'title' => $this->translation($outcome['en'], $outcome['ar']),
            ]);
        }
    }

    private function lessonContent(Course $course, CoursePeriod $termOnePeriod): void
    {
        $lesson = Lesson::query()->updateOrCreate([
            'course_id' => $course->id,
            'course_period_id' => $termOnePeriod->id,
            'sort_order' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addWeek(),
        ], [
            'title' => $this->translation(
                'Lesson 1: Introduction to Real Numbers',
                'الحصة الأولى: مقدمة الأعداد الحقيقية'
            ),
            'description' => $this->translation(
                'Introduction to real numbers with explanation, practice, homework, summary, and lesson exam.',
                'مقدمة في الأعداد الحقيقية مع شرح وتطبيقات وواجب وملخص وامتحان للحصة.'
            ),
            'is_active' => true,
        ]);

        $questionIds = $this->questions($lesson);

        $homeworkAssignment = Assignment::query()->updateOrCreate([
            'course_id' => $course->id,
        ], [
            'title' => $this->translation('Homework', 'الواجب المنزلي'),
            'description' => $this->translation(
                'Homework for the real numbers introduction lesson.',
                'واجب الحصة الخاصة بمقدمة الأعداد الحقيقية.'
            ),
            'num_of_questions' => 3,
            'num_of_easy_questions' => 1,
            'num_of_medium_questions' => 1,
            'num_of_hard_questions' => 1,
            'duration_minutes' => 30,
            'question_ids' => $questionIds,
        ]);

        $lessonExam = Exam::query()->updateOrCreate([
            'course_id' => $course->id,
        ], [
            'title' => $this->translation('Lesson Exam', 'امتحان الحصة'),
            'description' => $this->translation(
                'Short exam for the real numbers introduction lesson.',
                'امتحان قصير على حصة مقدمة الأعداد الحقيقية.'
            ),
            'num_of_questions' => 3,
            'num_of_easy_questions' => 1,
            'num_of_medium_questions' => 1,
            'num_of_hard_questions' => 1,
            'duration_minutes' => 20,
            'max_degree' => 10,
            'num_of_models' => 1,
            'lesson_ids' => [$lesson->id],
        ]);

        ExamModel::query()->updateOrCreate([
            'exam_id' => $lessonExam->id,
            'model_number' => 1,
        ], [
            'question_ids' => $this->examQuestionItems($questionIds, (float) $lessonExam->max_degree),
        ]);

        $this->lessonItems($lesson, $homeworkAssignment, $lessonExam);
    }

    /**
     * @return array<int, int>
     */
    private function questions(Lesson $lesson): array
    {
        $questions = [
            1 => [
                'title' => 'Which set contains the real numbers?',
                'type' => QuestionType::Mcq,
                'difficulty' => QuestionDifficulty::Easy,
                'options' => [
                    ['Rational and irrational numbers', true],
                    ['Only natural numbers', false],
                    ['Only integers', false],
                ],
            ],
            2 => [
                'title' => 'Every integer is a rational number.',
                'type' => QuestionType::TrueFalse,
                'difficulty' => QuestionDifficulty::Medium,
                'options' => [
                    ['True', true],
                    ['False', false],
                ],
            ],
            3 => [
                'title' => 'Explain the difference between rational and irrational numbers.',
                'type' => QuestionType::Statement,
                'difficulty' => QuestionDifficulty::Hard,
                'options' => [],
            ],
        ];

        return collect($questions)
            ->map(function (array $questionData, int $sortOrder) use ($lesson): int {
                $question = Question::query()->updateOrCreate([
                    'lesson_id' => $lesson->id,
                    'sort_order' => $sortOrder,
                ], [
                    'title' => $questionData['title'],
                    'type' => $questionData['type'],
                    'difficulty' => $questionData['difficulty'],
                ]);

                foreach ($questionData['options'] as $optionSortOrder => [$title, $isCorrect]) {
                    QuestionOption::query()->updateOrCreate([
                        'question_id' => $question->id,
                        'sort_order' => $optionSortOrder + 1,
                    ], [
                        'title' => $title,
                        'is_correct' => $isCorrect,
                    ]);
                }

                return $question->id;
            })
            ->values()
            ->all();
    }

    private function lessonItems(Lesson $lesson, Assignment $homeworkAssignment, Exam $lessonExam): void
    {
        foreach (
            [
                1 => [
                    'type' => LessonTypeEnum::Video,
                    'title' => $this->translation('Introduction to Real Numbers', 'مقدمة في الأعداد الحقيقية'),
                    'video_url' => 'courses/lesson_1/videos/01KXJYXHWGK3J6W8T2J7FT5338.mp4',
                    'duration_minutes' => 30,
                    'is_free' => true,
                ],
                2 => [
                    'type' => LessonTypeEnum::Link,
                    'title' => $this->translation('Second Explanation: Practical Applications', 'الشرح الثاني: تطبيقات عملية'),
                    'link_url' => 'https://www.youtube.com/',
                    'duration_minutes' => 25,
                    'is_free' => false,
                ],
                3 => [
                    'type' => LessonTypeEnum::Assignments,
                    'title' => $this->translation('Homework', 'الواجب المنزلي'),
                    'assignment_id' => $homeworkAssignment->id,
                    'duration_minutes' => 30,
                    'is_free' => false,
                ],
                4 => [
                    'type' => LessonTypeEnum::File,
                    'title' => $this->translation('Paper Summary (PDF)', 'الملخص الورقي (PDF)'),
                    'file_url' => 'courses/lesson_1/files/01KXJYHREW6FY3XBXR6SQ1JSPQ.png',
                    'is_free' => false,
                ],
                5 => [
                    'type' => LessonTypeEnum::Exams,
                    'title' => $this->translation('Lesson Exam', 'امتحان الحصة'),
                    'exam_id' => $lessonExam->id,
                    'duration_minutes' => 20,
                    'is_free' => false,
                ],
            ] as $sortOrder => $item
        ) {
            $lessonItem = LessonItem::query()->updateOrCreate([
                'lesson_id' => $lesson->id,
                'sort_order' => $sortOrder,
                'starts_at' => now(),
                'ends_at' => now()->addWeek(),
            ], [
                ...$item,
                'is_active' => true,
            ]);
        }
    }

    /**
     * @param  array<int, int>  $questionIds
     * @return array<int, array{id: int, max_score: float}>
     */
    private function examQuestionItems(array $questionIds, float $maxDegree): array
    {
        $maxScore = $questionIds === []
            ? 0
            : round($maxDegree / count($questionIds), 2);

        return collect($questionIds)
            ->map(fn (int $questionId): array => [
                'id' => $questionId,
                'max_score' => $maxScore,
            ])
            ->all();
    }
}
