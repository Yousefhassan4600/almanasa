<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhySmsService
{
    public function sendOtp(string $phone, string $code): void
    {
        $token = (string) config('services.whysms.api_token');
        $senderId = (string) config('services.whysms.sender_id');
        $endpoint = (string) config('services.whysms.endpoint');

        if (blank($token) || blank($senderId) || blank($endpoint)) {
            throw new RuntimeException('WHYSMS configuration is incomplete.');
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->post($endpoint, [
                    'api_token' => $token,
                    'recipient' => $phone,
                    'sender_id' => $senderId,
                    'type' => 'plain',
                    'message' => str_replace(':code', $code, (string) config('services.whysms.otp_message')),
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('WHYSMS connection failed.', previous: $exception);
        }

        if (! $response->successful() || $response->json('status') !== 'success') {
            throw new RuntimeException('WHYSMS rejected the verification message.');
        }
    }
}
