<?php

namespace App\Http\Controllers\Api\Academy;
use App\Actions\StudentPortal\Catalog\ListAccountSubjects;
use App\Actions\StudentPortal\Catalog\LoadTeachersPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\SubjectResource;
use App\Http\Responses\ApiResponse;
use App\Models\Provider;
use Illuminate\Http\Request;

class AcademyController extends Controller
{

public function getSubjects(Request $request, ListAccountSubjects $listAccountSubjects): ApiResponse
{
    $user = $request->attributes->get('auth_user');

    if (! $user) {
        return ApiResponse::make()
            ->success(false)
            ->message(__('unauthenticated'))
            ->statusCode(401);
    }

    $validated = $request->validate([
        'providerId' => ['required', 'integer', 'exists:providers,id'],
        'search'     => ['nullable', 'string', 'max:255'],
        'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        'page'       => ['nullable', 'integer', 'min:1'],
    ]);

    $gradeId  = $user->studentProfile()?->value('grade_id');
    $provider = Provider::query()->findOrFail($validated['providerId']);

    $search  = trim($validated['search'] ?? '');
    $perPage = (int) ($validated['per_page'] ?? 10);
    $page    = (int) ($validated['page'] ?? 1);

    $allSubjects = $listAccountSubjects->handle(
        $provider,
        $gradeId,
        $search,
        withActiveTeachersCount: true
    );

    $currentPageItems = $allSubjects->slice(($page - 1) * $perPage, $perPage)->values();

    $paginator = new LengthAwarePaginator(
        $currentPageItems,
        $allSubjects->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return ApiResponse::make()
        ->success(true)
        ->message(__('subjects_fetched_successfully'))
        ->data(SubjectResource::collection($paginator->items()))
        ->pagination($paginator);
}

public function getTeachers(Request $request, LoadTeachersPage $loadTeachersPage): ApiResponse
{
    $user = $request->attributes->get('auth_user');

    if (! $user) {
        return ApiResponse::make()
            ->success(false)
            ->message(__('unauthenticated'))
            ->statusCode(401);
    }

    $validated = $request->validate([
        'providerId' => ['required', 'integer', 'exists:providers,id'],
        'subjectId'  => ['required', 'integer', 'exists:account_subjects,id'],
        'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        'page'       => ['nullable', 'integer', 'min:1'],
    ]);

    $gradeId   = $user->studentProfile()?->value('grade_id');
    $provider  = Provider::query()->findOrFail($validated['providerId']);
    $subjectId = (int) $validated['subjectId'];

    $perPage = (int) ($validated['per_page'] ?? 10);
    $page    = (int) ($validated['page'] ?? 1);

    $data = $loadTeachersPage->handle(
        $provider,
        $gradeId,
        $subjectId
    );

    /** @var \Illuminate\Support\Collection $teachers */
    $teachers = $data['teachers'];
    $coursesByTeacher = $data['coursesByTeacher'];
    $isStandaloneTeacher = $data['isStandaloneTeacher'];

    $currentPageItems = $teachers->slice(($page - 1) * $perPage, $perPage)->values();

    $paginator = new LengthAwarePaginator(
        $currentPageItems,
        $teachers->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $resourceCollection = TeacherResource::collection($paginator->items())
        ->additional([
            'courses_by_teacher'    => $coursesByTeacher,
            'is_standalone_teacher' => $isStandaloneTeacher,
            'account_subject_id'    => $data['selectedSubjectId'],
        ]);

    return ApiResponse::make()
        ->success(true)
        ->message(__('teachers_fetched_successfully'))
        ->data($resourceCollection)
        ->pagination($paginator);
}

}
