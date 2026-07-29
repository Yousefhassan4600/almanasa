<?php

namespace App\Actions\StudentPortal\Layout;

use App\Models\Provider;

class LoadProviderTheme
{
    /**
     * @return array{provider: Provider, themeColor: string}
     */
    public function handle(int $providerId): array
    {
        $provider = Provider::query()->findOrFail($providerId);

        return [
            'provider' => $provider,
            'themeColor' => $provider->websitePrimaryColor(),
        ];
    }
}
