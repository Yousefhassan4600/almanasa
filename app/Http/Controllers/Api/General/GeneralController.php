<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Resources\CountryResource;
use App\Http\Resources\EducationStageResource;
use App\Http\Resources\GradeResource;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Support\GeneralSettingsCache;
use Illuminate\Support\Facades\Cache;




class GeneralController extends Controller
{
    public function getCountries(Request $request)
    {
        $data = Cache::remember(
            GeneralSettingsCache::for('countries', ['locale' => app()->getLocale()]),
            now()->addMinutes(10),
            fn(): array => CountryResource::collection(
                Country::query()
                    ->orderBy('id', 'asc')
                    ->get()
            )->resolve($request),
        );

        return ApiResponse::make()
            ->message(__('countries_fetched_successfully'))
            ->data($data)
            ->toResponse($request);
    }

    public function getCities(Request $request)
    {
        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);

        $countryId = (int) $validated['country_id'];

        $data = Cache::remember(
            GeneralSettingsCache::for('cities', [
                'countries' => $countryId,
                'locale' => app()->getLocale(),
            ]),
            now()->addMinutes(10),
            fn(): array => CityResource::collection(
                City::query()
                    ->where('country_id', $countryId)
                    ->orderBy('id', 'asc')
                    ->get()
            )->resolve($request),
        );

        return ApiResponse::make()
            ->message(__('cities_fetched_successfully'))
            ->data($data)
            ->toResponse($request);
    }

    public function getEducationalStages(Request $request)
    {
        $data = Cache::remember(
            GeneralSettingsCache::for('educational_stages', ['locale' => app()->getLocale()]),
            now()->addMinutes(10),
            fn(): array => EducationStageResource::collection(
                EducationStage::query()
                    ->orderBy('id', 'asc')
                    ->get()
            )->resolve($request),
        );

        return ApiResponse::make()
            ->message(__('educational_stages_fetched_successfully'))
            ->data($data)
            ->toResponse($request);
    }

    public function getGrades(Request $request)
    {
        $validated = $request->validate([
            'education_stage_id' => ['required', 'integer', 'exists:education_stages,id'],
        ]);

        $educationStageId = (int) $validated['education_stage_id'];

        $data = Cache::remember(
            GeneralSettingsCache::for('grades', [
                'education_stages' => $educationStageId,
                'locale' => app()->getLocale(),
            ]),
            now()->addMinutes(10),
            fn(): array => GradeResource::collection(
                Grade::query()
                    ->where('education_stage_id', $educationStageId)
                    ->orderBy('id', 'asc')
                    ->get()
            )->resolve($request),
        );

        return ApiResponse::make()
            ->message(__('grades_fetched_successfully'))
            ->data($data)
            ->toResponse($request);
    }
}
