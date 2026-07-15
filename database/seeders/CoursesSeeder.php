<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\CoursePeriodType;
use App\Enums\LessonTypeEnum;
use App\Enums\PurchaseUnitType;
use App\Models\AcademyTeacher;
use App\Models\AccountSubject;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\CoursePeriod;
use App\Models\CoursePrice;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\ProviderCode;
use App\Models\PurchaseUnit;

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
        $this->providerCode($academyProvider, $monthPurchaseUnit);
        $this->courseOutcomes($academyCourse);
        $this->lessonContent($academyProvider, $academyCourse, $termOnePeriod);
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

    private function providerCode(Provider $provider, PurchaseUnit $monthPurchaseUnit): void
    {
        ProviderCode::query()->updateOrCreate([
            'provider_id' => $provider->id,
            'code' => 'FUTURE-STARS-MONTH',
        ], [
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

    private function lessonContent(Provider $provider, Course $course, CoursePeriod $termOnePeriod): void
    {
        $lesson = Lesson::query()->updateOrCreate([
            'course_id' => $course->id,
            'course_period_id' => $termOnePeriod->id,
            'sort_order' => 1,
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

        $homeworkAssignment = Assignment::query()->updateOrCreate([
            'provider_id' => $provider->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ], [
            'title' => $this->translation('Homework', 'الواجب المنزلي'),
            'description' => $this->translation(
                'Homework for the real numbers introduction lesson.',
                'واجب الحصة الخاصة بمقدمة الأعداد الحقيقية.'
            ),
            'duration_minutes' => 30,
            'max_score' => 10,
            'allow_retake' => true,
            'max_attempts' => 3,
            'status' => ContentStatus::Published,
            'published_at' => now(),
        ]);

        $lessonExam = Exam::query()->updateOrCreate([
            'provider_id' => $provider->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ], [
            'title' => $this->translation('Lesson Exam', 'امتحان الحصة'),
            'description' => $this->translation(
                'Short exam for the real numbers introduction lesson.',
                'امتحان قصير على حصة مقدمة الأعداد الحقيقية.'
            ),
            'duration_minutes' => 20,
            'max_score' => 10,
            'pass_score' => 5,
            'max_attempts' => 1,
            'stop_on_page_leave' => false,
            'status' => ContentStatus::Published,
            'published_at' => now(),
        ]);

        $this->lessonItems($lesson, $homeworkAssignment, $lessonExam);
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
                    'type' => LessonTypeEnum::Assignment,
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
                    'type' => LessonTypeEnum::Exam,
                    'title' => $this->translation('Lesson Exam', 'امتحان الحصة'),
                    'exam_id' => $lessonExam->id,
                    'duration_minutes' => 20,
                    'is_free' => false,
                ],
            ] as $sortOrder => $item
        ) {
            LessonItem::query()->updateOrCreate([
                'lesson_id' => $lesson->id,
                'sort_order' => $sortOrder,
            ], [
                ...$item,
                'is_active' => true,
            ]);
        }
    }
}
