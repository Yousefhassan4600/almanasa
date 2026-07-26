<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BunnyStreamService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BunnyStreamVideoController extends Controller
{
    public function destroy(Request $request, BunnyStreamService $bunnyStream): JsonResponse
    {
        $data = $request->validate([
            'video_id' => ['required', 'string', 'max:100'],
        ]);

        try {
            $bunnyStream->deleteVideo($data['video_id']);

            return response()->json([
                'message' => __('admin.messages.bunny_video_deleted'),
            ]);
        } catch (RequestException $exception) {
            report($exception);

            return response()->json([
                'message' => __('admin.messages.bunny_video_delete_failed'),
            ], $exception->response->status() ?: 502);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage() ?: __('admin.messages.bunny_video_delete_failed'),
            ], 500);
        }
    }
}
