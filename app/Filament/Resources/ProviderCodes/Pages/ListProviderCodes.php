<?php

namespace App\Filament\Resources\ProviderCodes\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\ProviderCodes\ProviderCodeResource;
use App\Filament\Support\CurrentAccount;
use Filament\Actions\CreateAction;

class ListProviderCodes extends BaseListRecords
{
    protected static string $resource = ProviderCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    if (! CurrentAccount::isSaasOwner()) {
                        $data['provider_id'] = CurrentAccount::providerId();
                    }

                    return $data;
                }),
        ];
    }
}
