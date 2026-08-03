<?php

namespace App\Http\Resources;

use App\Enums\PurchaseUnitType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $additionalData      = $this->additional ?? [];
        $coursesByTeacher    = $additionalData['courses_by_teacher'] ?? collect();
        $isStandaloneTeacher = $additionalData['is_standalone_teacher'] ?? false;
        $accountSubjectId    = $additionalData['account_subject_id'] ?? null;

        $isAccountModel = $this->resource instanceof \App\Models\Account;

        $teacherName = $isStandaloneTeacher
            ? ($this->owner?->name ?? 'معلم')
            : ($this->teacher?->owner?->name ?? 'معلم');

        $teacherImage = (! $isStandaloneTeacher && $this->image)
            ? asset('storage/'.$this->image)
            : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200';

        $teacherCourses = $coursesByTeacher->get($this->id, collect());

        $monthlyPrices = $teacherCourses
            ->flatMap->prices
            ->filter(fn ($price) => $price->purchaseUnit?->type === PurchaseUnitType::Month)
            ->map(fn ($price) => $price->offer_price ?? $price->price)
            ->filter();

        $monthlyPrice = $monthlyPrices->isNotEmpty()
            ? $monthlyPrices->min()
            : $teacherCourses->flatMap->prices->map(fn ($price) => $price->offer_price ?? $price->price)->filter()->min();

        $weeklyLectures = $teacherCourses->pluck('weekly_lectures_count')->filter()->max();

        $teacherUrl = $isStandaloneTeacher
            ? "/single_teacher?subject={$accountSubjectId}"
            : "/single_teacher?teacher={$this->id}&subject={$accountSubjectId}";

        return [
            'id'                   => $this->id,
            'name'                 => $teacherName,
            'image'                => $teacherImage,
            'experience_years'     => $this->experience_years ?? 0,
            'monthly_price'        => $monthlyPrice ? (float) $monthlyPrice : null,
            'monthly_price_formatted' => $monthlyPrice ? number_format((float) $monthlyPrice) . ' EGP' : '—',
            'weekly_lectures_count'=> $weeklyLectures ?: null,
            'details_url'          => $teacherUrl,
        ];
    }
}
