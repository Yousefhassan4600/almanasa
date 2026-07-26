<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BunnyStreamService
{
    /**
     * @return array{
     *     videoId: string,
     *     libraryId: string,
     *     expirationTime: int,
     *     signature: string,
     *     embedUrl: string,
     *     title: string,
     *     tusEndpoint: string
     * }
     *
     * @throws RequestException
     */
    public function createTusUpload(string $title): array
    {
        $libraryId = $this->libraryId();
        $apiKey = $this->apiKey();

        $response = Http::acceptJson()
            ->timeout(10)
            ->connectTimeout(5)
            ->retry([100, 500])
            ->withHeaders([
                'AccessKey' => $apiKey,
            ])
            ->post($this->baseUrl()."/library/{$libraryId}/videos", [
                'title' => $title,
            ])
            ->throw()
            ->json();

        $videoId = $response['guid'] ?? null;

        if (! is_string($videoId) || $videoId === '') {
            throw new RuntimeException('Bunny Stream did not return a video ID.');
        }

        $expirationTime = now()->addSeconds($this->uploadExpirationSeconds())->timestamp;

        return [
            'videoId' => $videoId,
            'libraryId' => $libraryId,
            'expirationTime' => $expirationTime,
            'signature' => hash('sha256', $libraryId.$apiKey.$expirationTime.$videoId),
            'embedUrl' => $this->embedUrl($videoId),
            'title' => $title,
            'tusEndpoint' => $this->baseUrl().'/tusupload',
        ];
    }

    /**
     * @throws RequestException
     */
    public function deleteVideo(string $videoId): void
    {
        Http::acceptJson()
            ->timeout(10)
            ->connectTimeout(5)
            ->retry([100, 500])
            ->withHeaders([
                'AccessKey' => $this->apiKey(),
            ])
            ->delete($this->baseUrl().'/library/'.$this->libraryId().'/videos/'.$videoId)
            ->throw();
    }

    public function signedEmbedUrl(?string $videoUrlOrId, int $ttlSeconds): ?string
    {
        $videoId = $this->videoIdFrom($videoUrlOrId);

        if (! $videoId) {
            return null;
        }

        $parameters = $this->embedPlayerParameters();

        if ($this->embedTokenAuthenticationEnabled() || $this->debugEnabled()) {
            $expires = now()->addSeconds($this->signedEmbedTtlSeconds($ttlSeconds))->timestamp;

            $parameters = [
                'token' => hash('sha256', $this->embedTokenKey().$videoId.$expires),
                'expires' => $expires,
                ...$parameters,
            ];
        }

        return $this->embedUrl($videoId).'?'.http_build_query($parameters);
    }

    public function videoIdFrom(?string $videoUrlOrId): ?string
    {
        if (! is_string($videoUrlOrId) || trim($videoUrlOrId) === '') {
            return null;
        }

        $value = trim($videoUrlOrId);

        if (! str_contains($value, '/')) {
            return $value;
        }

        $path = parse_url($value, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $videoId = end($segments);

        if (is_string($videoId) && str_contains($videoId, '.') && count($segments) > 1) {
            $videoId = $segments[count($segments) - 2];
        }

        return is_string($videoId) && $videoId !== '' ? $videoId : null;
    }

    private function libraryId(): string
    {
        $libraryId = config('services.bunny_stream.library_id');

        if (! is_string($libraryId) || $libraryId === '') {
            throw new RuntimeException('Bunny Stream library ID is not configured.');
        }

        return $libraryId;
    }

    private function apiKey(): string
    {
        $apiKey = config('services.bunny_stream.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('Bunny Stream API key is not configured.');
        }

        return $apiKey;
    }

    private function embedTokenKey(): string
    {
        $embedTokenKey = config('services.bunny_stream.embed_token_key') ?: $this->apiKey();

        if (! is_string($embedTokenKey) || $embedTokenKey === '') {
            throw new RuntimeException('Bunny Stream embed token key is not configured.');
        }

        return $embedTokenKey;
    }

    private function baseUrl(): string
    {
        $baseUrl = rtrim((string) config('services.bunny_stream.base_url', 'https://video.bunnycdn.com'), '/');

        if (str_contains($baseUrl, 'b-cdn.net')) {
            throw new RuntimeException('Bunny Stream API base URL must be https://video.bunnycdn.com, not the CDN playback URL.');
        }

        return $baseUrl;
    }

    private function embedUrl(string $videoId): string
    {
        $embedBaseUrl = rtrim((string) config('services.bunny_stream.embed_base_url', 'https://iframe.mediadelivery.net/embed'), '/');

        return $embedBaseUrl.'/'.$this->libraryId().'/'.$videoId;
    }

    private function uploadExpirationSeconds(): int
    {
        return max(3600, (int) config('services.bunny_stream.upload_expiration_seconds', 86400));
    }

    private function embedTokenAuthenticationEnabled(): bool
    {
        return (bool) config('services.bunny_stream.embed_token_authentication_enabled', true);
    }

    private function debugEnabled(): bool
    {
        return (bool) config('services.bunny_stream.debug', false);
    }

    private function signedEmbedTtlSeconds(int $ttlSeconds): int
    {
        $ttlSeconds = max(60, $ttlSeconds);

        return $this->debugEnabled()
            ? max($ttlSeconds, 86400)
            : $ttlSeconds;
    }

    /**
     * @return array<string, string>
     */
    private function embedPlayerParameters(): array
    {
        return [
            'autoplay' => 'false',
            'preload' => 'false',
        ];
    }
}
