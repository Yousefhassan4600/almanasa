<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Models\Account;
use App\Models\Role;

class CreateRole extends BaseCreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $permissionFormData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionFormData = $data;

        $account = $this->currentAccount();

        $data['provider_id'] = $account?->provider_id;
        $data['created_by_account_id'] = $account?->id;

        return RoleForm::stripPermissionFormState($data);
    }

    protected function afterCreate(): void
    {
        if ($this->record instanceof Role) {
            RoleForm::syncRolePermissions($this->record, $this->permissionFormData);
        }
    }

    private function currentAccount(): ?Account
    {
        $account = request()->attributes->get('current_account');

        if ($account instanceof Account) {
            return $account;
        }

        $accountId = (int) request()->session()->get('current_account_id');

        if ($accountId <= 0) {
            return null;
        }

        return Account::query()->find($accountId);
    }
}
