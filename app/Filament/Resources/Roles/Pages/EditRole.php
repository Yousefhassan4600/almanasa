<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Models\Role;

class EditRole extends BaseEditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $permissionFormData = [];

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        if ($this->isAcademyTeacherSystemRole()) {
            return;
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    protected function getFormActions(): array
    {
        if ($this->isAcademyTeacherSystemRole()) {
            return [
                $this->getCancelFormAction(),
            ];
        }

        return parent::getFormActions();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->isAcademyTeacherSystemRole()) {
            $this->permissionFormData = [];

            return [
                'provider_id' => $this->record->provider_id,
                'created_by_account_id' => $this->record->created_by_account_id,
                'name' => $this->record->name,
                'guard_name' => $this->record->guard_name,
                'is_assignable' => $this->record->is_assignable,
            ];
        }

        $this->permissionFormData = $data;

        return RoleForm::stripPermissionFormState($data);
    }

    protected function afterSave(): void
    {
        if ($this->record instanceof Role && ! $this->isAcademyTeacherSystemRole()) {
            RoleForm::syncRolePermissions($this->record, $this->permissionFormData);
        }
    }

    private function isAcademyTeacherSystemRole(): bool
    {
        return $this->record instanceof Role
            && RoleForm::isAcademyTeacherSystemRole($this->record);
    }
}
