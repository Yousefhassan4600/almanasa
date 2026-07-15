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
        $elementary = $this->stage('Elementary', 'المرحلة الابتدائية', 1);
        $primary = $this->stage('Primary', 'المرحلة الإعدادية', 2);
        $secondary = $this->stage('Secondary', 'المرحلة الثانوية', 3);

        foreach (
            [
                [$elementary, 'One', 'الصف الأول', 1],
                [$elementary, 'Two', 'الصف الثاني', 2],
                [$elementary, 'Three', 'الصف الثالث', 3],
                [$elementary, 'Four', 'الصف الرابع', 4],
                [$elementary, 'Five', 'الصف الخامس', 5],
                [$elementary, 'Six', 'الصف السادس', 6],
                [$primary, 'One', 'الصف الأول', 7],
                [$primary, 'Two', 'الصف الثاني', 8],
                [$primary, 'Three', 'الصف الثالث', 9],
                [$secondary, 'One', 'الصف الأول', 10],
                [$secondary, 'Two', 'الصف الثاني', 11],
                [$secondary, 'Three', 'الصف الثالث', 12],
            ] as [$stage, $nameEn, $nameAr, $sortOrder]
        ) {
            $this->grade($stage, $nameEn, $nameAr, $sortOrder);
        }

        $this->track('general', 'General', 'عام', 0);
        $scientificTrack = $this->track('scientific', 'Scientific', 'علمي', 1);
        $this->track('literary', 'Literary', 'أدبي', 2);
        $scientificMathTrack = $this->track('scientific_math', 'Scientific (Mathematics)', 'علمي رياضة', 3);
        $this->track('scientific_sciences', 'Scientific (Sciences)', 'علمي علوم', 4);

        $secondaryOne = Grade::query()
            ->where('education_stage_id', $secondary->id)
            ->where('sort_order', 10)
            ->firstOrFail();

        $secondaryTwo = Grade::query()
            ->where('education_stage_id', $secondary->id)
            ->where('sort_order', 11)
            ->firstOrFail();

        $math = $this->subject(
            $scientificMathTrack,
            'Mathematics',
            'الرياضيات',
            'Core secondary mathematics subject.',
            'مادة الرياضيات الأساسية للمرحلة الثانوية.'
        );

        $physics = $this->subject(
            $scientificTrack,
            'Physics',
            'الفيزياء',
            'Secondary physics subject.',
            'مادة الفيزياء للمرحلة الثانوية.'
        );

        GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $math->id,
        ]);

        GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryOne->id,
            'subject_id' => $physics->id,
        ]);

        GradeSubject::query()->firstOrCreate([
            'grade_id' => $secondaryTwo->id,
            'subject_id' => $math->id,
        ]);
    }

    private function stage(string $nameEn, string $nameAr, int $sortOrder): EducationStage
    {
        return EducationStage::query()->firstOrCreate([
            'sort_order' => $sortOrder,
        ], [
            'name' => $this->translation($nameEn, $nameAr),
        ]);
    }

    private function grade(EducationStage $stage, string $nameEn, string $nameAr, int $sortOrder): Grade
    {
        return Grade::query()->firstOrCreate([
            'education_stage_id' => $stage->id,
            'sort_order' => $sortOrder,
        ], [
            'name' => $this->translation($nameEn, $nameAr),
        ]);
    }

    private function track(string $code, string $nameEn, string $nameAr, int $sortOrder): Track
    {
        return Track::query()->firstOrCreate([
            'code' => $code,
        ], [
            'name' => $this->translation($nameEn, $nameAr),
            'code' => $code,
            'sort_order' => $sortOrder,
        ]);
    }

    private function subject(
        Track $track,
        string $nameEn,
        string $nameAr,
        string $descriptionEn,
        string $descriptionAr,
    ): Subject {
        return Subject::query()
            ->where('track_id', $track->id)
            ->where('name->en', $nameEn)
            ->firstOrCreate([], [
                'track_id' => $track->id,
                'name' => $this->translation($nameEn, $nameAr),
                'description' => $this->translation($descriptionEn, $descriptionAr),
            ]);
    }
}
