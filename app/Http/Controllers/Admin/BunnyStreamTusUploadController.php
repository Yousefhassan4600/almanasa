<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Support\CurrentAccount;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\BunnyStreamService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BunnyStreamTusUploadController extends Controller
{
    public function __invoke(Request $request, BunnyStreamService $bunnyStream): JsonResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'lesson_id' => ['required', 'integer', 'exists:lessons,id'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_type' => ['nullable', 'string', 'max:100'],
        ]);

        $lesson = CurrentAccount::scopeLessonsToCurrentAccount(
            Lesson::query()
                ->whereKey($data['lesson_id'])
                ->where('course_id', $data['course_id'])
        )->firstOrFail();

        $title = 'Course'.$lesson->course_id.'-Lesson'.$lesson->id;

        try {
            return response()->json($bunnyStream->createTusUpload($title));
        } catch (RequestException $exception) {
            report($exception);

            return response()->json([
                'message' => __('admin.messages.bunny_video_credentials_failed'),
            ], $exception->response->status() ?: 502);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage() ?: __('admin.messages.bunny_video_credentials_failed'),
            ], 500);
        }
    }
}
