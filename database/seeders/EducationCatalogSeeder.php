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

        $tracks = $this->tracks();
        $grades = $this->grades($stages);
        $gradeTracks = $this->gradeTracks();
        $validGradeSubjectIds = [];

        foreach ($this->subjectCoverages() as $subjectKey => $coverageKeys) {
            $subject = $this->subject($subjectKey);

            foreach ($coverageKeys as $coverageKey) {
                $gradeTrack = $gradeTracks[$coverageKey];

                $validGradeSubjectIds[] = $this->gradeSubject(
                    $grades[$gradeTrack['grade']],
                    $tracks[$gradeTrack['track']],
                    $subject,
                )->id;
            }
        }

        $this->deleteMissingGradeSubjects($validGradeSubjectIds);
        $this->deleteUnusedSubjects();
        $this->deleteUnusedTracks();
    }

    /**
     * @return array<string, Track>
     */
    private function tracks(): array
    {
        return [
            'general' => $this->track('general', 'General', 'عام', 0),
            'secondary_old_scientific' => $this->track('secondary_old_scientific', 'Secondary Old System Scientific', 'ثانوية نظام قديم علمي', 1),
            'secondary_old_literary' => $this->track('secondary_old_literary', 'Secondary Old System Literary', 'ثانوية نظام قديم أدبي', 2),
            'secondary_old_scientific_math' => $this->track('secondary_old_scientific_math', 'Secondary Old System Scientific Math', 'ثانوية نظام قديم علمي رياضة', 3),
            'secondary_old_scientific_science' => $this->track('secondary_old_scientific_science', 'Secondary Old System Scientific Science', 'ثانوية نظام قديم علمي علوم', 4),
            'secondary_new_literature_arts' => $this->track('secondary_new_literature_arts', 'Secondary New System Literature and Arts Track', 'بكالوريا مسار الآداب والفنون', 5),
            'secondary_new_business' => $this->track('secondary_new_business', 'Secondary New System Business Track', 'بكالوريا مسار الأعمال', 6),
            'secondary_new_medicine_life_sciences' => $this->track('secondary_new_medicine_life_sciences', 'Secondary New System Medicine and Life Sciences Track', 'بكالوريا مسار الطب وعلوم الحياة', 7),
            'secondary_new_engineering_computer_science' => $this->track('secondary_new_engineering_computer_science', 'Secondary New System Engineering and Computer Science Track', 'بكالوريا الهندسة وعلوم الحاسب', 8),
        ];
    }

    /**
     * @param  array<string, EducationStage>  $stages
     * @return array<string, Grade>
     */
    private function grades(array $stages): array
    {
        return [
            'elementary_1' => $this->grade($stages['elementary'], 'First Elementary', 'الصف الأول الابتدائي', 1),
            'elementary_2' => $this->grade($stages['elementary'], 'Second Elementary', 'الصف الثاني الابتدائي', 2),
            'elementary_3' => $this->grade($stages['elementary'], 'Third Elementary', 'الصف الثالث الابتدائي', 3),
            'elementary_4' => $this->grade($stages['elementary'], 'Fourth Elementary', 'الصف الرابع الابتدائي', 4),
            'elementary_5' => $this->grade($stages['elementary'], 'Fifth Elementary', 'الصف الخامس الابتدائي', 5),
            'elementary_6' => $this->grade($stages['elementary'], 'Sixth Elementary', 'الصف السادس الابتدائي', 6),
            'preparatory_1' => $this->grade($stages['preparatory'], 'First Preparatory', 'الصف الأول الإعدادي', 7),
            'preparatory_2' => $this->grade($stages['preparatory'], 'Second Preparatory', 'الصف الثاني الإعدادي', 8),
            'preparatory_3' => $this->grade($stages['preparatory'], 'Third Preparatory', 'الصف الثالث الإعدادي', 9),
            'secondary_1' => $this->grade($stages['secondary'], 'First Secondary', 'الصف الأول الثانوي', 10),
            'secondary_2' => $this->grade($stages['secondary'], 'Second Secondary', 'الصف الثاني الثانوي', 11),
            'secondary_3' => $this->grade($stages['secondary'], 'Third Secondary', 'الصف الثالث الثانوي', 12),
        ];
    }

    /**
     * @return array<string, array{grade: string, track: string}>
     */
    private function gradeTracks(): array
    {
        $gradeTracks = [];

        foreach (range(1, 6) as $gradeNumber) {
            $gradeTracks[$this->coverageKey("elementary_{$gradeNumber}", 'general')] = [
                'grade' => "elementary_{$gradeNumber}",
                'track' => 'general',
            ];
        }

        foreach (range(1, 3) as $gradeNumber) {
            $gradeTracks[$this->coverageKey("preparatory_{$gradeNumber}", 'general')] = [
                'grade' => "preparatory_{$gradeNumber}",
                'track' => 'general',
            ];
        }

        $gradeTracks[$this->coverageKey('secondary_1', 'general')] = [
            'grade' => 'secondary_1',
            'track' => 'general',
        ];

        foreach (
            [
                'secondary_old_scientific',
                'secondary_old_literary',
                'secondary_new_literature_arts',
                'secondary_new_business',
                'secondary_new_medicine_life_sciences',
                'secondary_new_engineering_computer_science',
            ] as $trackCode
        ) {
            $gradeTracks[$this->coverageKey('secondary_2', $trackCode)] = [
                'grade' => 'secondary_2',
                'track' => $trackCode,
            ];
        }

        foreach (
            [
                'secondary_old_scientific_math',
                'secondary_old_scientific_science',
                'secondary_old_literary',
                'secondary_new_literature_arts',
                'secondary_new_business',
                'secondary_new_medicine_life_sciences',
                'secondary_new_engineering_computer_science',
            ] as $trackCode
        ) {
            $gradeTracks[$this->coverageKey('secondary_3', $trackCode)] = [
                'grade' => 'secondary_3',
                'track' => $trackCode,
            ];
        }

        return $gradeTracks;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function subjectCoverages(): array
    {
        $allGradeTracks = array_keys($this->gradeTracks());

        $frenchCoverage = [
            ...$this->generalElementary(3, 4, 5, 6),
            ...$this->generalPreparatory(1, 2, 3),
            ...$this->secondary(1, 'general'),
            ...$this->secondary(2, 'secondary_old_scientific', 'secondary_old_literary', 'secondary_new_literature_arts'),
            ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_old_literary'),
        ];

        $italianGermanCoverage = [
            ...$this->generalPreparatory(1, 2, 3),
            ...$this->secondary(1, 'general'),
            ...$this->secondary(2, 'secondary_old_scientific', 'secondary_old_literary', 'secondary_new_literature_arts'),
            ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_old_literary'),
        ];

        $mathCoverage = [
            ...$this->generalElementary(1, 2, 3, 4, 5, 6),
            ...$this->generalPreparatory(1, 2, 3),
            ...$this->secondary(1, 'general'),
            ...$this->secondary(2, 'secondary_new_medicine_life_sciences'),
            ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_new_business', 'secondary_new_engineering_computer_science'),
        ];

        $pureMathCoverage = [
            ...$this->secondary(2, 'secondary_old_scientific', 'secondary_old_literary'),
        ];

        $scienceCoverage = [
            ...$this->generalElementary(4, 5, 6),
            ...$this->generalPreparatory(1, 2, 3),
        ];

        return [
            'arabic' => $allGradeTracks,
            'english_ol' => $allGradeTracks,
            'english_al' => $allGradeTracks,
            'french' => $frenchCoverage,
            'italian' => $italianGermanCoverage,
            'german' => $italianGermanCoverage,
            'math_ar' => $mathCoverage,
            'math_en' => $mathCoverage,
            'pure_math_ar' => $pureMathCoverage,
            'pure_math_en' => $pureMathCoverage,
            'applied_math_ar' => $this->secondary(2, 'secondary_old_scientific'),
            'applied_math_en' => $this->secondary(2, 'secondary_old_scientific'),
            'science_ar' => $scienceCoverage,
            'science_en' => $scienceCoverage,
            'integrated_science_ar' => $this->secondary(1, 'general'),
            'integrated_science_en' => $this->secondary(1, 'general'),
            'discover_ar' => $this->generalElementary(1, 2, 3),
            'discover_en' => $this->generalElementary(1, 2, 3),
            'chemistry_ar' => [
                ...$this->secondary(2, 'secondary_old_scientific', 'secondary_new_engineering_computer_science'),
                ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_new_medicine_life_sciences'),
            ],
            'chemistry_en' => [
                ...$this->secondary(2, 'secondary_old_scientific', 'secondary_new_engineering_computer_science'),
                ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_new_medicine_life_sciences'),
            ],
            'physics_ar' => [
                ...$this->secondary(2, 'secondary_old_scientific', 'secondary_new_medicine_life_sciences'),
                ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_new_engineering_computer_science'),
            ],
            'physics_en' => [
                ...$this->secondary(2, 'secondary_old_scientific', 'secondary_new_medicine_life_sciences'),
                ...$this->secondary(3, 'secondary_old_scientific_math', 'secondary_old_scientific_science', 'secondary_new_engineering_computer_science'),
            ],
            'biology_ar' => [
                ...$this->secondary(3, 'secondary_old_scientific_science', 'secondary_new_medicine_life_sciences'),
            ],
            'biology_en' => [
                ...$this->secondary(3, 'secondary_old_scientific_science', 'secondary_new_medicine_life_sciences'),
            ],
            'social_studies' => [
                ...$this->generalElementary(4, 5, 6),
                ...$this->generalPreparatory(1, 2, 3),
            ],
            'history' => [
                ...$this->secondary(1, 'general'),
                ...$this->secondary(2, 'secondary_old_scientific', 'secondary_old_literary', 'secondary_new_business', 'secondary_new_engineering_computer_science', 'secondary_new_medicine_life_sciences', 'secondary_new_literature_arts'),
                ...$this->secondary(3, 'secondary_old_literary'),
            ],
            'geography' => [
                ...$this->secondary(2, 'secondary_old_literary'),
                ...$this->secondary(3, 'secondary_old_literary', 'secondary_new_literature_arts'),
            ],
            'philosophy_logic' => $this->secondary(1, 'general'),
            'philosophy' => $this->secondary(3, 'secondary_old_literary', 'secondary_new_business'),
            'psychology' => [
                ...$this->secondary(2, 'secondary_old_literary', 'secondary_new_literature_arts'),
                ...$this->secondary(3, 'secondary_old_literary'),
            ],
            'ict' => $this->generalElementary(4, 5, 6),
            'computer' => $this->generalPreparatory(1, 2, 3),
            'programming' => [
                ...$this->secondary(1, 'general'),
                ...$this->secondary(2, 'secondary_new_engineering_computer_science'),
            ],
            'accounting_ar' => $this->secondary(2, 'secondary_new_business'),
            'accounting_en' => $this->secondary(2, 'secondary_new_business'),
            'statistics_ar' => $this->secondary(3, 'secondary_old_literary', 'secondary_new_literature_arts'),
            'statistics_en' => $this->secondary(3, 'secondary_old_literary', 'secondary_new_literature_arts'),
            'business_management_ar' => $this->secondary(2, 'secondary_new_business'),
            'business_management_en' => $this->secondary(2, 'secondary_new_business'),
            'economy_ar' => $this->secondary(3, 'secondary_new_business'),
            'economy_en' => $this->secondary(3, 'secondary_new_business'),
        ];
    }

    /**
     * @return array<string, array{en: string, ar: string, name: string}>
     */
    private function subjectNames(): array
    {
        return [
            'arabic' => ['en' => 'Arabic', 'ar' => 'عربي', 'name' => 'عربي'],
            'english_ol' => ['en' => 'English (O.L)', 'ar' => 'English (O.L)', 'name' => 'English (O.L)'],
            'english_al' => ['en' => 'English (A.L)', 'ar' => 'English (A.L)', 'name' => 'English (A.L)'],
            'french' => ['en' => 'French', 'ar' => 'French', 'name' => 'French'],
            'italian' => ['en' => 'Italian', 'ar' => 'Italian', 'name' => 'Italian'],
            'german' => ['en' => 'German', 'ar' => 'German', 'name' => 'German'],
            'math_ar' => ['en' => 'Mathematics', 'ar' => 'رياضيات', 'name' => 'رياضيات'],
            'math_en' => ['en' => 'Math', 'ar' => 'Math', 'name' => 'Math'],
            'pure_math_ar' => ['en' => 'Pure Mathematics', 'ar' => 'رياضة بحتة', 'name' => 'رياضة بحتة'],
            'pure_math_en' => ['en' => 'Pure Math', 'ar' => 'Pure Math', 'name' => 'Pure Math'],
            'applied_math_ar' => ['en' => 'Applied Mathematics', 'ar' => 'رياضة تطبيقية', 'name' => 'رياضة تطبيقية'],
            'applied_math_en' => ['en' => 'Applied Math', 'ar' => 'Applied Math', 'name' => 'Applied Math'],
            'science_ar' => ['en' => 'Science', 'ar' => 'علوم', 'name' => 'علوم'],
            'science_en' => ['en' => 'Science', 'ar' => 'Science', 'name' => 'Science'],
            'integrated_science_ar' => ['en' => 'Integrated Science', 'ar' => 'علوم متكاملة', 'name' => 'علوم متكاملة'],
            'integrated_science_en' => ['en' => 'Integrated Science', 'ar' => 'Integrated Science', 'name' => 'Integrated Science'],
            'discover_ar' => ['en' => 'Discover', 'ar' => 'اكتشف', 'name' => 'اكتشف'],
            'discover_en' => ['en' => 'Discover', 'ar' => 'Discover', 'name' => 'Discover'],
            'chemistry_ar' => ['en' => 'Chemistry', 'ar' => 'كيمياء', 'name' => 'كيمياء'],
            'chemistry_en' => ['en' => 'Chemistry', 'ar' => 'Chemistry', 'name' => 'Chemistry'],
            'physics_ar' => ['en' => 'Physics', 'ar' => 'فيزياء', 'name' => 'فيزياء'],
            'physics_en' => ['en' => 'Physics', 'ar' => 'Physics', 'name' => 'Physics'],
            'biology_ar' => ['en' => 'Biology', 'ar' => 'أحياء', 'name' => 'أحياء'],
            'biology_en' => ['en' => 'Biology', 'ar' => 'Biology', 'name' => 'Biology'],
            'social_studies' => ['en' => 'Social Studies', 'ar' => 'دراسات', 'name' => 'دراسات'],
            'history' => ['en' => 'History', 'ar' => 'تاريخ', 'name' => 'تاريخ'],
            'geography' => ['en' => 'Geography', 'ar' => 'جغرافيا', 'name' => 'جغرافيا'],
            'philosophy_logic' => ['en' => 'Philosophy and Logic', 'ar' => 'فلسفة ومنطق', 'name' => 'فلسفة ومنطق'],
            'philosophy' => ['en' => 'Philosophy', 'ar' => 'فلسفة', 'name' => 'فلسفة'],
            'psychology' => ['en' => 'Psychology', 'ar' => 'علم نفس', 'name' => 'علم نفس'],
            'ict' => ['en' => 'ICT', 'ar' => 'ICT', 'name' => 'ICT'],
            'computer' => ['en' => 'Computer', 'ar' => 'حاسب آلي', 'name' => 'حاسب آلي'],
            'programming' => ['en' => 'Programming', 'ar' => 'برمجة', 'name' => 'برمجة'],
            'accounting_ar' => ['en' => 'Accounting', 'ar' => 'محاسبة', 'name' => 'محاسبة'],
            'accounting_en' => ['en' => 'Accounting', 'ar' => 'Accounting', 'name' => 'Accounting'],
            'statistics_ar' => ['en' => 'Statistics', 'ar' => 'إحصاء', 'name' => 'إحصاء'],
            'statistics_en' => ['en' => 'Statistics', 'ar' => 'Statistics', 'name' => 'Statistics'],
            'business_management_ar' => ['en' => 'Business Management', 'ar' => 'إدارة أعمال', 'name' => 'إدارة أعمال'],
            'business_management_en' => ['en' => 'Business Management', 'ar' => 'Business Management', 'name' => 'Business Management'],
            'economy_ar' => ['en' => 'Economy', 'ar' => 'اقتصاد', 'name' => 'اقتصاد'],
            'economy_en' => ['en' => 'Economy', 'ar' => 'Economy', 'name' => 'Economy'],
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

    private function subject(string $subjectKey): Subject
    {
        $name = $this->subjectNames()[$subjectKey];

        /** @var Subject|null $subject */
        $subject = Subject::query()
            ->withTrashed()
            ->where('name', $name['name'])
            ->first();

        $subject ??= new Subject;

        $subject->fill([
            'name' => $name['name'],
            'description' => $name['name'],
        ]);

        $subject->restore();
        $subject->save();

        return $subject;
    }

    private function gradeSubject(Grade $grade, Track $track, Subject $subject): GradeSubject
    {
        /** @var GradeSubject $gradeSubject */
        $gradeSubject = GradeSubject::query()
            ->withTrashed()
            ->firstOrNew([
                'grade_id' => $grade->id,
                'track_id' => $track->id,
                'subject_id' => $subject->id,
            ]);

        $gradeSubject->restore();
        $gradeSubject->save();

        return $gradeSubject;
    }

    /**
     * @return array<int, string>
     */
    private function generalElementary(int ...$gradeNumbers): array
    {
        return array_map(
            fn (int $gradeNumber): string => $this->coverageKey("elementary_{$gradeNumber}", 'general'),
            $gradeNumbers,
        );
    }

    /**
     * @return array<int, string>
     */
    private function generalPreparatory(int ...$gradeNumbers): array
    {
        return array_map(
            fn (int $gradeNumber): string => $this->coverageKey("preparatory_{$gradeNumber}", 'general'),
            $gradeNumbers,
        );
    }

    /**
     * @return array<int, string>
     */
    private function secondary(int $gradeNumber, string ...$trackCodes): array
    {
        return array_map(
            fn (string $trackCode): string => $this->coverageKey("secondary_{$gradeNumber}", $trackCode),
            $trackCodes,
        );
    }

    private function coverageKey(string $gradeKey, string $trackCode): string
    {
        return "{$gradeKey}|{$trackCode}";
    }

    /**
     * @param  array<int, int>  $gradeSubjectIds
     */
    private function deleteMissingGradeSubjects(array $gradeSubjectIds): void
    {
        GradeSubject::query()
            ->whereNotIn('id', $gradeSubjectIds)
            ->get()
            ->each(fn (GradeSubject $gradeSubject) => $gradeSubject->delete());
    }

    private function deleteUnusedSubjects(): void
    {
        Subject::query()
            ->whereDoesntHave('gradeSubjects')
            ->get()
            ->each(fn (Subject $subject) => $subject->delete());
    }

    private function deleteUnusedTracks(): void
    {
        Track::query()
            ->whereDoesntHave('gradeSubjects')
            ->get()
            ->each(fn (Track $track) => $track->delete());
    }
}
