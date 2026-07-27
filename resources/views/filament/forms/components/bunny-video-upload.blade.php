@php
    $courseId = $courseId ?? null;
    $lessonId = $lessonId ?? null;
    $uploadUrl = route('admin.bunny-stream.tus-upload');
    $deleteUrl = route('admin.bunny-stream.videos.destroy');
    $statePath = $getStatePath();
    $bunnyVideoIdStatePath = str_contains($statePath, '.')
        ? str($statePath)->beforeLast('.')->append('.bunny_video_id')->toString()
        : 'bunny_video_id';
    $durationSecondsStatePath = str_contains($statePath, '.')
        ? str($statePath)->beforeLast('.')->append('.duration_seconds')->toString()
        : 'duration_seconds';
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
            bunnyVideoIdState: $wire.{{ $applyStateBindingModifiers("\$entangle('{$bunnyVideoIdStatePath}')") }},
            durationSecondsState: $wire.{{ $applyStateBindingModifiers("\$entangle('{$durationSecondsStatePath}')") }},
            uploading: false,
            progress: 0,
            error: null,
            fileName: null,
            courseId: @js($courseId),
            lessonId: @js($lessonId),
            uploadUrl: @js($uploadUrl),
            deleteUrl: @js($deleteUrl),
            csrfToken: @js(csrf_token()),

            extractBunnyVideoId(url) {
                if (! url || typeof url !== 'string') {
                    return null;
                }

                const parts = url.split('/').filter(Boolean);
                const videoId = parts.length ? parts[parts.length - 1] : null;

                if (videoId && videoId.includes('.') && parts.length > 1) {
                    return parts[parts.length - 2];
                }

                return videoId;
            },

            videoDurationSeconds(file) {
                return new Promise((resolve) => {
                    const video = document.createElement('video');
                    const objectUrl = URL.createObjectURL(file);

                    video.preload = 'metadata';
                    video.onloadedmetadata = () => {
                        URL.revokeObjectURL(objectUrl);
                        resolve(Number.isFinite(video.duration) ? Math.ceil(video.duration) : null);
                    };
                    video.onerror = () => {
                        URL.revokeObjectURL(objectUrl);
                        resolve(null);
                    };
                    video.src = objectUrl;
                });
            },

            async deleteBunnyVideo(videoId) {
                if (! videoId) {
                    return;
                }

                const response = await fetch(this.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({
                        video_id: videoId,
                    }),
                });

                if (! response.ok) {
                    throw new Error(@js(__('admin.messages.bunny_video_delete_failed')));
                }
            },

            async loadTus() {
                if (window.tus) {
                    return;
                }

                await new Promise((resolve, reject) => {
                    const existingScript = document.querySelector('script[data-bunny-tus-client]');

                    if (existingScript) {
                        existingScript.addEventListener('load', resolve, { once: true });
                        existingScript.addEventListener('error', reject, { once: true });

                        return;
                    }

                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/tus-js-client@4.3.1/dist/tus.min.js';
                    script.async = true;
                    script.dataset.bunnyTusClient = 'true';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            },

            async upload(event) {
                const file = event.target.files?.[0] ?? null;

                this.error = null;
                this.progress = 0;
                this.fileName = file?.name ?? null;

                if (! file) {
                    return;
                }

                if (! this.courseId || ! this.lessonId) {
                    this.error = @js(__('admin.messages.save_lesson_before_video_upload'));
                    event.target.value = null;

                    return;
                }

                if (! file.type.startsWith('video/')) {
                    this.error = @js(__('admin.messages.video_file_required'));
                    event.target.value = null;

                    return;
                }

                this.uploading = true;

                let credentials = null;
                const previousVideoId = this.bunnyVideoIdState || this.extractBunnyVideoId(this.state);

                try {
                    const durationSeconds = await this.videoDurationSeconds(file);

                    if (durationSeconds) {
                        this.durationSecondsState = durationSeconds;
                    }

                    await this.loadTus();

                    const response = await fetch(this.uploadUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify({
                            course_id: this.courseId,
                            lesson_id: this.lessonId,
                            file_name: file.name,
                            file_type: file.type,
                        }),
                    });

                    const contentType = response.headers.get('content-type') ?? '';
                    credentials = contentType.includes('application/json')
                        ? await response.json()
                        : { message: @js(__('admin.messages.bunny_video_credentials_failed')) };

                    if (! response.ok) {
                        throw new Error(credentials.message ?? credentials.error ?? @js(__('admin.messages.bunny_video_credentials_failed')));
                    }

                    const upload = new window.tus.Upload(file, {
                        endpoint: credentials.tusEndpoint,
                        retryDelays: [0, 3000, 5000, 10000, 20000, 60000],
                        fingerprint: () => Promise.resolve(`bunny-stream-${credentials.libraryId}-${credentials.videoId}`),
                        headers: {
                            AuthorizationSignature: credentials.signature,
                            AuthorizationExpire: `${credentials.expirationTime}`,
                            VideoId: credentials.videoId,
                            LibraryId: `${credentials.libraryId}`,
                        },
                        metadata: {
                            filetype: file.type,
                            title: credentials.title,
                        },
                        onError: (error) => {
                            this.error = error?.message ?? @js(__('admin.messages.bunny_video_upload_failed'));
                            this.uploading = false;

                            if (credentials?.videoId) {
                                this.deleteBunnyVideo(credentials.videoId).catch(() => {});
                            }
                        },
                        onProgress: (bytesUploaded, bytesTotal) => {
                            this.progress = bytesTotal > 0
                                ? Math.round((bytesUploaded / bytesTotal) * 100)
                                : 0;
                        },
                        onSuccess: () => {
                            this.state = credentials.embedUrl;
                            this.bunnyVideoIdState = credentials.videoId;
                            this.progress = 100;
                            this.fileName = @js(__('admin.messages.bunny_video_processing'));
                            this.uploading = false;

                            if (previousVideoId && previousVideoId !== credentials.videoId) {
                                this.deleteBunnyVideo(previousVideoId).catch((error) => {
                                    this.error = error?.message ?? @js(__('admin.messages.bunny_video_delete_failed'));
                                });
                            }
                        },
                    });

                    upload.start();
                } catch (error) {
                    this.error = error?.message ?? @js(__('admin.messages.bunny_video_upload_failed'));
                    this.uploading = false;

                    if (credentials?.videoId) {
                        this.deleteBunnyVideo(credentials.videoId).catch(() => {});
                    }
                }
            },
        }"
        class="space-y-3"
    >
        <template x-if="state">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; border: 1px solid #bbf7d0; border-radius: 0.625rem; background: #f0fdf4; padding: 0.75rem 0.875rem;">
                <span style="color: #15803d; font-size: 0.875rem; font-weight: 700; line-height: 1.25rem;">
                    {{ __('admin.messages.bunny_video_ready') }}
                </span>

                <a
                    x-bind:href="state"
                    target="_blank"
                    style="display: inline-flex; min-height: 2rem; align-items: center; justify-content: center; border-radius: 0.5rem; background: #16a34a; padding: 0.375rem 0.875rem; color: #fff; font-size: 0.8125rem; font-weight: 800; line-height: 1.125rem; text-decoration: none;"
                >
                    {{ __('admin.labels.Open') }}
                </a>
            </div>
        </template>

        <template x-if="! courseId || ! lessonId">
            <div class="rounded-lg border border-warning-200 bg-warning-50 px-3 py-2 text-sm text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-300">
                {{ __('admin.messages.save_lesson_before_video_upload') }}
            </div>
        </template>

        <div style="display: flex; flex-wrap: wrap; align-items: stretch; gap: 1rem; padding-top: 0.25rem;">
            <input
                x-ref="videoInput"
                type="file"
                accept="video/*"
                x-bind:disabled="uploading || ! courseId || ! lessonId"
                x-on:change="upload"
                style="display: none;"
            />

            <button
                type="button"
                x-bind:disabled="uploading || ! courseId || ! lessonId"
                x-on:click="$refs.videoInput.click()"
                style="display: inline-flex; min-height: 2.5rem; align-items: center; justify-content: center; gap: 0.5rem; border: 0; border-radius: 0.5rem; background: #d97706; padding: 0.625rem 1rem; color: #fff; font-size: 0.875rem; font-weight: 700; line-height: 1.25rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08); cursor: pointer;"
                x-bind:style="(uploading || ! courseId || ! lessonId)
                    ? 'display: inline-flex; min-height: 2.5rem; align-items: center; justify-content: center; gap: 0.5rem; border: 0; border-radius: 0.5rem; background: #d1d5db; padding: 0.625rem 1rem; color: #6b7280; font-size: 0.875rem; font-weight: 700; line-height: 1.25rem; box-shadow: none; cursor: not-allowed;'
                    : 'display: inline-flex; min-height: 2.5rem; align-items: center; justify-content: center; gap: 0.5rem; border: 0; border-radius: 0.5rem; background: #d97706; padding: 0.625rem 1rem; color: #fff; font-size: 0.875rem; font-weight: 700; line-height: 1.25rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08); cursor: pointer;'"
            >
                <span aria-hidden="true">+</span>
                <span x-text="state ? @js(__('admin.labels.Re-upload Other Video')) : @js(__('admin.labels.Choose File'))"></span>
            </button>

            <span
                style="display: flex; min-height: 2.5rem; min-width: 14rem; flex: 1 1 16rem; align-items: center; overflow: hidden; border: 1px solid #d1d5db; border-radius: 0.5rem; background: #f9fafb; padding: 0.625rem 1.125rem; color: #374151; font-size: 0.875rem; line-height: 1.25rem; text-overflow: ellipsis; white-space: nowrap;"
                x-text="fileName ?? @js(__('admin.labels.No file chosen'))"
            ></span>
        </div>

        <template x-if="uploading">
            <div class="space-y-1">
                <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-white/10">
                    <div
                        class="h-full rounded-full bg-primary-600 transition-all"
                        x-bind:style="`width: ${progress}%`"
                    ></div>
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    <span x-text="fileName"></span>
                    <span> - </span>
                    <span x-text="`${progress}%`"></span>
                </div>
            </div>
        </template>

        <template x-if="error">
            <div class="rounded-lg border border-danger-200 bg-danger-50 px-3 py-2 text-sm text-danger-700 dark:border-danger-800 dark:bg-danger-950 dark:text-danger-300">
                <span x-text="error"></span>
            </div>
        </template>
    </div>
</x-dynamic-component>
