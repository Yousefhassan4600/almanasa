<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Enums\AccountType;
use App\Enums\ProviderType;
use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Providers\ProviderResource;
use App\Models\Account;

class CreateProvider extends BaseCreateRecord
{
    protected static string $resource = ProviderResource::class;

    protected function afterCreate(): void
    {
        Account::query()->firstOrCreate([
            'provider_id' => $this->record->id,
            'type' => $this->accountType()->value,
            'owner_user_id' => $this->record->owner_user_id,
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    private function accountType(): AccountType
    {
        return match ($this->record->type) {
            ProviderType::Academy => AccountType::Academy,
            ProviderType::StandaloneTeacher => AccountType::StandaloneTeacher,
        };
    }
}
