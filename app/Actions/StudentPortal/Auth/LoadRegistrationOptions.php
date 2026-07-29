<?php

namespace App\Actions\StudentPortal\Auth;

use App\Enums\Gender;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Collection;

class LoadRegistrationOptions
{
    /**
     * @return array{countries: Collection<int, Country>, cities: Collection<int, City>, educationStages: Collection<int, EducationStage>, grades: Collection<int, Grade>, genders: array<string, string>}
     */
    public function handle(?int $countryId, ?int $educationStageId): array
    {
        return [
            'countries' => Country::query()->orderBy('name')->get(),
            'cities' => City::query()
                ->when($countryId, fn ($query) => $query->where('country_id', $countryId))
                ->orderBy('name')
                ->get(),
            'educationStages' => EducationStage::query()->orderBy('sort_order')->get(),
            'grades' => Grade::query()
                ->when($educationStageId, fn ($query) => $query->where('education_stage_id', $educationStageId))
                ->orderBy('sort_order')
                ->get(),
            'genders' => Gender::options(),
        ];
    }
}
