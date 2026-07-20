<?php

namespace Database\Seeders;

use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\GradeSubject;
use App\Models\Subject;
use App\Models\Track;

class EducationCatalogSeeder extends BaseSeeder
{
    public function run(): void
    {
        $stages = [
            'elementary' => $this->stage('Elementary Stage', 'المرحلة الابتدائية', 1),
            'preparatory' => $this->stage('Preparatory Stage', 'المرحلة الإعدادية', 2),
            'secondary' => $this->stage('Secondary Stage', 'المرحلة الثانوية', 3),
        ];

        $tracks = [
            'general' => $this->track('general', 'General', 'عام', 0),
            'scientific' => $this->track('scientific', 'Scientific', 'علمي', 1),
            'literary' => $this->track('literary', 'Literary', 'أدبي', 2),
            'scientific_math' => $this->track('scientific_math', 'Scientific Mathematics', 'علمي رياضيات', 3),
            'scientific_sciences' => $this->track('scientific_sciences', 'Scientific Sciences', 'علمي علوم', 4),
        ];

        foreach ($this->catalog() as $gradeData) {
            $grade = $this->grade(
                $stages[$gradeData['stage']],
                $gradeData['name_en'],
                $gradeData['name_ar'],
                $gradeData['sort_order'],
            );

            $subjectIds = [];

            foreach ($gradeData['subjects'] as $subjectData) {
                $subject = $this->subject(
                    $tracks[$subjectData['track']],
                    $subjectData['key'],
                );

                $this->gradeSubject($grade, $subject);
                $subjectIds[] = $subject->id;
            }

            $this->deleteMissingGradeSubjects($grade, $subjectIds);
        }

        $this->deleteObsoleteSecondarySplitGrades($stages['secondary']);
        $this->deleteUnusedSubjects();
    }

    /**
     * @return array<int, array{
     *     stage: string,
     *     name_en: string,
     *     name_ar: string,
     *     sort_order: int,
     *     subjects: array<int, array{key: string, track: string}>
     * }>
     */
    private function catalog(): array
    {
        $earlyPrimarySubjects = [
            $this->subjectData('arabic'),
            $this->subjectData('mathematics'),
            $this->subjectData('english'),
        ];

        $upperPrimarySubjects = [
            ...$earlyPrimarySubjects,
            $this->subjectData('social_studies'),
            $this->subjectData('science'),
        ];

        $secondaryCommonLanguageSubjects = [
            $this->subjectData('arabic'),
            $this->subjectData('english'),
            $this->subjectData('french'),
            $this->subjectData('italian'),
        ];

        $secondaryScientificSubjects = [
            $this->subjectData('mathematics'),
            $this->subjectData('chemistry', 'scientific'),
            $this->subjectData('physics', 'scientific'),
            $this->subjectData('biology', 'scientific'),
        ];

        $secondaryLiterarySubjects = [
            $this->subjectData('history', 'literary'),
            $this->subjectData('geography', 'literary'),
            $this->subjectData('philosophy_logic', 'literary'),
            $this->subjectData('psychology_sociology', 'literary'),
        ];

        return [
            [
                'stage' => 'elementary',
                'name_en' => 'First Elementary',
                'name_ar' => 'الصف الأول الابتدائي',
                'sort_order' => 1,
                'subjects' => $earlyPrimarySubjects,
            ],
            [
                'stage' => 'elementary',
                'name_en' => 'Second Elementary',
                'name_ar' => 'الصف الثاني الابتدائي',
                'sort_order' => 2,
                'subjects' => $earlyPrimarySubjects,
            ],
            [
                'stage' => 'elementary',
                'name_en' => 'Third Elementary',
                'name_ar' => 'الصف الثالث الابتدائي',
                'sort_order' => 3,
                'subjects' => $earlyPrimarySubjects,
            ],
            [
                'stage' => 'elementary',
                'name_en' => 'Fourth Elementary',
                'name_ar' => 'الصف الرابع الابتدائي',
                'sort_order' => 4,
                'subjects' => $upperPrimarySubjects,
            ],
            [
                'stage' => 'elementary',
                'name_en' => 'Fifth Elementary',
                'name_ar' => 'الصف الخامس الابتدائي',
                'sort_order' => 5,
                'subjects' => $upperPrimarySubjects,
            ],
            [
                'stage' => 'elementary',
                'name_en' => 'Sixth Elementary',
                'name_ar' => 'الصف السادس الابتدائي',
                'sort_order' => 6,
                'subjects' => $upperPrimarySubjects,
            ],
            [
                'stage' => 'preparatory',
                'name_en' => 'First Preparatory',
                'name_ar' => 'الصف الأول الإعدادي',
                'sort_order' => 7,
                'subjects' => $upperPrimarySubjects,
            ],
            [
                'stage' => 'preparatory',
                'name_en' => 'Second Preparatory',
                'name_ar' => 'الصف الثاني الإعدادي',
                'sort_order' => 8,
                'subjects' => $upperPrimarySubjects,
            ],
            [
                'stage' => 'preparatory',
                'name_en' => 'Third Preparatory',
                'name_ar' => 'الصف الثالث الإعدادي',
                'sort_order' => 9,
                'subjects' => [
                    ...$upperPrimarySubjects,
                    $this->subjectData('french'),
                    $this->subjectData('italian'),
                ],
            ],
            [
                'stage' => 'secondary',
                'name_en' => 'First Secondary',
                'name_ar' => 'الصف الأول الثانوي',
                'sort_order' => 10,
                'subjects' => [
                    $this->subjectData('arabic'),
                    $this->subjectData('mathematics'),
                    $this->subjectData('english'),
                    $this->subjectData('french'),
                    $this->subjectData('italian'),
                    $this->subjectData('history', 'literary'),
                    $this->subjectData('geography', 'literary'),
                    $this->subjectData('chemistry', 'scientific'),
                    $this->subjectData('physics', 'scientific'),
                    $this->subjectData('biology', 'scientific'),
                    $this->subjectData('philosophy_logic', 'literary'),
                    $this->subjectData('psychology_sociology', 'literary'),
                ],
            ],
            [
                'stage' => 'secondary',
                'name_en' => 'Second Secondary',
                'name_ar' => 'الصف الثاني الثانوي',
                'sort_order' => 11,
                'subjects' => [
                    ...$secondaryCommonLanguageSubjects,
                    ...$secondaryScientificSubjects,
                    ...$secondaryLiterarySubjects,
                ],
            ],
            [
                'stage' => 'secondary',
                'name_en' => 'Third Secondary',
                'name_ar' => 'الصف الثالث الثانوي',
                'sort_order' => 12,
                'subjects' => [
                    ...$secondaryCommonLanguageSubjects,
                    $this->subjectData('chemistry', 'scientific'),
                    $this->subjectData('physics', 'scientific'),
                    $this->subjectData('biology', 'scientific'),
                    $this->subjectData('pure_mathematics', 'scientific_math'),
                    $this->subjectData('applied_mathematics', 'scientific_math'),
                    $this->subjectData('geology', 'scientific_sciences'),
                    ...$secondaryLiterarySubjects,
                ],
            ],
        ];
    }

    /**
     * @return array{key: string, track: string}
     */
    private function subjectData(string $subjectKey, string $trackCode = 'general'): array
    {
        return [
            'key' => $subjectKey,
            'track' => $trackCode,
        ];
    }

    /**
     * @return array<string, array{en: string, ar: string}>
     */
    private function subjectNames(): array
    {
        return [
            'arabic' => ['en' => 'Arabic Language', 'ar' => 'اللغة العربية'],
            'mathematics' => ['en' => 'Mathematics', 'ar' => 'الرياضيات'],
            'english' => ['en' => 'English Language', 'ar' => 'اللغة الإنجليزية'],
            'social_studies' => ['en' => 'Social Studies', 'ar' => 'الدراسات الاجتماعية'],
            'science' => ['en' => 'Science', 'ar' => 'العلوم'],
            'french' => ['en' => 'French Language', 'ar' => 'اللغة الفرنسية'],
            'italian' => ['en' => 'Italian Language', 'ar' => 'اللغة الإيطالية'],
            'history' => ['en' => 'History', 'ar' => 'التاريخ'],
            'geography' => ['en' => 'Geography', 'ar' => 'الجغرافيا'],
            'chemistry' => ['en' => 'Chemistry', 'ar' => 'الكيمياء'],
            'physics' => ['en' => 'Physics', 'ar' => 'الفيزياء'],
            'biology' => ['en' => 'Biology', 'ar' => 'الأحياء'],
            'philosophy_logic' => ['en' => 'Philosophy and Logic', 'ar' => 'فلسفة و منطق'],
            'psychology_sociology' => ['en' => 'Psychology and Sociology', 'ar' => 'علم نفس واجتماع'],
            'pure_mathematics' => ['en' => 'Pure Mathematics', 'ar' => 'الرياضيات البحتة'],
            'applied_mathematics' => ['en' => 'Applied Mathematics', 'ar' => 'الرياضيات التكاملية'],
            'geology' => ['en' => 'Geology', 'ar' => 'جيولوجيا'],
        ];
    }

    private function stage(string $nameEn, string $nameAr, int $sortOrder): EducationStage
    {
        /** @var EducationStage $stage */
        $stage = EducationStage::query()
            ->withTrashed()
            ->firstOrNew([
                'sort_order' => $sortOrder,
            ]);

        $stage->fill([
            'name' => $this->translation($nameEn, $nameAr),
        ]);

        $stage->restore();
        $stage->save();

        return $stage;
    }

    private function grade(EducationStage $stage, string $nameEn, string $nameAr, int $sortOrder): Grade
    {
        /** @var Grade $grade */
        $grade = Grade::query()
            ->withTrashed()
            ->firstOrNew([
                'education_stage_id' => $stage->id,
                'sort_order' => $sortOrder,
            ]);

        $grade->fill([
            'name' => $this->translation($nameEn, $nameAr),
        ]);

        $grade->restore();
        $grade->save();

        return $grade;
    }

    private function track(string $code, string $nameEn, string $nameAr, int $sortOrder): Track
    {
        /** @var Track $track */
        $track = Track::query()
            ->withTrashed()
            ->firstOrNew([
                'code' => $code,
            ]);

        $track->fill([
            'name' => $this->translation($nameEn, $nameAr),
            'code' => $code,
            'sort_order' => $sortOrder,
        ]);

        $track->restore();
        $track->save();

        return $track;
    }

    private function subject(Track $track, string $subjectKey): Subject
    {
        $name = $this->subjectNames()[$subjectKey];

        /** @var Subject|null $subject */
        $subject = Subject::query()
            ->withTrashed()
            ->where('track_id', $track->id)
            ->get()
            ->first(fn (Subject $subject): bool => $subject->getTranslation('name', 'ar') === $name['ar']);

        $subject ??= new Subject([
            'track_id' => $track->id,
        ]);

        $subject->fill([
            'track_id' => $track->id,
            'name' => $this->translation($name['en'], $name['ar']),
            'description' => $this->translation($name['en'], $name['ar']),
        ]);

        $subject->restore();
        $subject->save();

        return $subject;
    }

    private function gradeSubject(Grade $grade, Subject $subject): GradeSubject
    {
        /** @var GradeSubject $gradeSubject */
        $gradeSubject = GradeSubject::query()
            ->withTrashed()
            ->firstOrNew([
                'grade_id' => $grade->id,
                'subject_id' => $subject->id,
            ]);

        $gradeSubject->restore();
        $gradeSubject->save();

        return $gradeSubject;
    }

    /**
     * @param  array<int, int>  $subjectIds
     */
    private function deleteMissingGradeSubjects(Grade $grade, array $subjectIds): void
    {
        $grade->gradeSubjects()
            ->whereNotIn('subject_id', $subjectIds)
            ->delete();
    }

    private function deleteObsoleteSecondarySplitGrades(EducationStage $secondary): void
    {
        Grade::query()
            ->withTrashed()
            ->where('education_stage_id', $secondary->id)
            ->where('sort_order', '>', 12)
            ->get()
            ->each(function (Grade $grade): void {
                $grade->gradeSubjects()->delete();
                $grade->delete();
            });
    }

    private function deleteUnusedSubjects(): void
    {
        Subject::query()
            ->whereDoesntHave('gradeSubjects')
            ->get()
            ->each(fn (Subject $subject) => $subject->delete());
    }
}
