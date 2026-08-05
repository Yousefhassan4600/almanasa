<?php

namespace App\Http\Controllers\Api\StudentLearning;
use App\Actions\StudentPortal\Catalog\ListAccountSubjects;
use App\Actions\StudentPortal\Catalog\LoadSingleTeacherPage;
use App\Actions\StudentPortal\Catalog\LoadTeachersPage;
use App\Actions\StudentPortal\Courses\CheckCourseSubscription;
use App\Actions\StudentPortal\Lessons\CalculateAssessmentAttempts;
use App\Actions\StudentPortal\Lessons\ManageLessonVideoPlayback;
use App\Actions\StudentPortal\Lessons\ResolveLessonItem;
use App\Actions\StudentPortal\Students\LoadMyLessons;
use App\Enums\LessonTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\LessonItemResource;
use App\Http\Resources\MySubscribedSubjectResource;
use App\Http\Resources\SingleTeacherPageResource;
use App\Http\Resources\TeacherResource;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\SubjectResource;
use App\Http\Responses\ApiResponse;
use App\Models\Provider;
use Illuminate\Http\Request;

class StudentLearningController extends Controller
{

public function getMySubscribedSubjects( Request $request,  LoadMyLessons $loadMyLessons): ApiResponse {
    $user = $request->attributes->get('auth_user');

    if (! $user) {
        return ApiResponse::make()
            ->success(false)
            ->message(__('unauthenticated'))
            ->statusCode(401);
    }

    $validated = $request->validate([
        'providerId' => ['required', 'integer', 'exists:providers,id'],
    ]);

    $provider = Provider::query()->findOrFail($validated['providerId']);

    $data = $loadMyLessons->handle($provider, $user);
    $subscriptions = $data['subscriptions'];

    return ApiResponse::make()
        ->success(true)
        ->message(__('subjects_fetched_successfully'))
        ->data(MySubscribedSubjectResource::collection($subscriptions));
}

public function getSingleTeacherPage(
    Request $request,
    LoadSingleTeacherPage $loadSingleTeacherPage, CheckCourseSubscription $checkCourseSubscription,
): ApiResponse {
    $user = $request->attributes->get('auth_user');

    if (! $user) {
        return ApiResponse::make()
            ->success(false)
            ->message(__('unauthenticated'))
            ->statusCode(401);
    }

    $validated = $request->validate([
        'providerId'     => ['required', 'integer', 'exists:providers,id'],
        'teacherId'      => ['nullable', 'integer', 'exists:academy_teachers,id'],
        'subjectId'      => ['nullable', 'integer', 'exists:account_subjects,id'],
        'coursePeriodId' => ['nullable', 'integer', 'exists:course_periods,id'],
    ]);

    $provider       = Provider::query()->findOrFail($validated['providerId']);
    $gradeId        = $user->studentProfile()?->value('grade_id');
    $teacherId      = isset($validated['teacherId']) ? (int) $validated['teacherId'] : null;
    $subjectId      = isset($validated['subjectId']) ? (int) $validated['subjectId'] : null;
    $coursePeriodId = isset($validated['coursePeriodId']) ? (int) $validated['coursePeriodId'] : null;

    $data = $loadSingleTeacherPage->handle(
        $provider,
        $teacherId,
        $subjectId,
        $user->id,
        $gradeId
    );

    if (! $data['teacher']) {
        return ApiResponse::make()
            ->success(false)
            ->message(__('teacher_not_found'))
            ->statusCode(404);
    }
    $course = $data['course'] ?? null;
$hasCourseSubscription = false;

if ($course) {
    $hasCourseSubscription = $checkCourseSubscription->handle($course, $user->id);
}

    $data['hasCourseSubscription'] = $hasCourseSubscription;
    $data['userId'] = $user->id;
    $data['coursePeriodId'] = $coursePeriodId;

    return ApiResponse::make()
        ->success(true)
        ->message(__('teacher_page_fetched_successfully'))
        ->data(new SingleTeacherPageResource($data));
}


    public function getLessonItem(
        Request $request,
        ResolveLessonItem $resolveLessonItem,
        CheckCourseSubscription $checkCourseSubscription,
        ManageLessonVideoPlayback $manageLessonVideoPlayback,
        CalculateAssessmentAttempts $calculateAssessmentAttempts
    ) {
        $validated = $request->validate([
            'providerId'   => ['required', 'integer', 'exists:providers,id'],
            'lessonItemId' => ['required', 'integer', 'exists:lesson_items,id'],
        ]);

        $user = $request->attributes->get('auth_user') ?? Auth::user();

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('unauthenticated'))
                ->statusCode(401);
        }

        $provider = Provider::query()->findOrFail($validated['providerId']);

        $lessonItem = $resolveLessonItem->handle($provider, $validated['lessonItemId']);

        if (! $lessonItem) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('lesson_item_not_found'))
                ->statusCode(404);
        }

        if (! $lessonItem->isCurrentlyOpen()) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('lesson_item_not_available_yet'))
                ->statusCode(403);
        }

        $course = $lessonItem->lesson?->course;
        $hasCourseSubscription = $course
            ? $checkCourseSubscription->handle($course, $user->id)
            : false;

        if (! $lessonItem->is_free && ! $hasCourseSubscription) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('subscription_required_to_access_content'))
                ->statusCode(403);
        }

        $videoPlayback = null;
        if ($lessonItem->type === LessonTypeEnum::Video || $lessonItem->video_url || $lessonItem->bunny_video_id) {
            $videoPlayback = $manageLessonVideoPlayback->resolve(
                $lessonItem,
                $user->id,
                $hasCourseSubscription
            );

            if ($videoPlayback['videoViewLimitReached'] ?? false) {
                return ApiResponse::make()
                    ->success(false)
                    ->message(__('video_view_limit_exceeded'))
                    ->statusCode(403);
            }

            $this->saveVideoProgress(
                $manageLessonVideoPlayback,
                $provider,
                $lessonItem->id,
                $user->id,
                $request
            );
        }

        $assessment = $lessonItem->assignment ?? $lessonItem->exam;
        $attempts = $assessment
            ? $calculateAssessmentAttempts->handle($assessment, $user->id)
            : null;

        $payload = [
            'lessonItem'            => $lessonItem,
            'hasCourseSubscription' => $hasCourseSubscription,
            'videoPlayback'        => $videoPlayback,
            'attempts'             => $attempts,
        ];

        return ApiResponse::make()
            ->success(true)
            ->message(__('lesson_item_fetched_successfully'))
            ->data(new LessonItemResource($payload));
    }

    private function saveVideoProgress(
        ManageLessonVideoPlayback $manageLessonVideoPlayback,
        Provider $provider,
        int $lessonItemId,
        int $userId,
        Request $request
    ): array {
        return $manageLessonVideoPlayback->saveProgress(
            $provider,
            $lessonItemId,
            $userId,
            (float) $request->input('positionSeconds', 0),
            (float) $request->input('durationSeconds', 0),
            (float) $request->input('watchedDeltaSeconds', 0),
            (bool) $request->input('ended', false),
            $request->input('progressId') ? (int) $request->input('progressId') : null
        );
    }





}
