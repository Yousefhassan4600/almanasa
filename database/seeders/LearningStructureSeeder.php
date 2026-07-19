<?php

namespace Database\Seeders;

use App\Enums\CoursePeriodType;
use App\Enums\PurchaseUnitType;
use App\Models\CoursePeriod;
use App\Models\PurchaseUnit;

class LearningStructureSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->purchaseUnits();
        $this->coursePeriods();
    }

    private function purchaseUnits(): void
    {
        foreach (
            [
                PurchaseUnitType::Lesson->value => ['Lesson', 'حصة', 7, 1],
                PurchaseUnitType::Month->value => ['Month', 'شهر', 30, 2],
                PurchaseUnitType::Term->value => ['Term', 'ترم', 120, 3],
                PurchaseUnitType::Year->value => ['Year', 'سنة', 365, 4],
            ] as $type => [$nameEn, $nameAr, $periodDays, $sortOrder]
        ) {
            PurchaseUnit::query()->updateOrCreate([
                'type' => $type,
            ], [
                'name' => $this->translation($nameEn, $nameAr),
                'period_days' => $periodDays,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }

    private function coursePeriods(): void
    {
        foreach (
            [
                CoursePeriodType::Term1->value => ['Term 1', 'الترم الأول', 1],
                CoursePeriodType::Term2->value => ['Term 2', 'الترم الثاني', 2],
                CoursePeriodType::Yearly->value => ['Yearly', 'العام الدراسي', 3],
            ] as $type => [$nameEn, $nameAr, $sortOrder]
        ) {
            CoursePeriod::query()->updateOrCreate([
                'type' => $type,
            ], [
                'name' => $this->translation($nameEn, $nameAr),
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }
}
