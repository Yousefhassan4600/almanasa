<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\AdminPermissionAction;
use App\Models\Role;
use App\Support\AdminPermissions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.Role Details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.Name'))
                            ->rule('not_in:'.AdminPermissions::ACADEMY_TEACHER_ROLE)
                            ->required(),
                        TextInput::make('guard_name')
                            ->label(__('admin.Guard Name'))
                            ->default('web')
                            ->readOnly()
                            ->required(),
                        Toggle::make('is_assignable')
                            ->label(__('admin.Assignable'))
                            ->default(true),
                        Toggle::make('permission_select_all')
                            ->label(__('admin.Select All'))
                            ->helperText(__('admin.messages.select_all_permissions'))
                            ->onIcon('heroicon-s-lock-open')
                            ->offIcon('heroicon-s-lock-closed')
                            ->live()
                            ->dehydrated()
                            ->afterStateHydrated(function (Toggle $component, ?Role $record): void {
                                $component->state($record instanceof Role && self::roleHasAllPermissions($record));
                            })
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                foreach (self::resourceKeys() as $resourceKey => $label) {
                                    $set(self::resourceToggleField($resourceKey), (bool) $state);

                                    foreach (AdminPermissionAction::cases() as $action) {
                                        $set(self::permissionField($resourceKey, $action), $action !== AdminPermissionAction::ViewHis && (bool) $state);
                                    }
                                }
                            }),
                    ])
                    ->disabled(fn (?Role $record): bool => self::isAcademyTeacherSystemRole($record))
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make(__('admin.Permissions'))
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'lg' => 2,
                            '2xl' => 3,
                        ])
                            ->schema(self::permissionCards()),
                    ])
                    ->disabled(fn (?Role $record): bool => self::isAcademyTeacherSystemRole($record))
                    ->columnSpanFull(),
            ]);
    }

    public static function isAcademyTeacherSystemRole(?Role $role): bool
    {
        return $role instanceof Role
            && $role->provider_id === null
            && $role->name === AdminPermissions::ACADEMY_TEACHER_ROLE;
    }

    /**
     * @return array<int, Section>
     */
    private static function permissionCards(): array
    {
        return collect(self::resourceKeys())
            ->map(fn (string $label, string $resourceKey): Section => self::permissionCard($resourceKey, $label))
            ->values()
            ->all();
    }

    private static function permissionCard(string $resourceKey, string $label): Section
    {
        return Section::make()
            ->schema([
                Toggle::make(self::resourceToggleField($resourceKey))
                    ->label($label)
                    ->onIcon('heroicon-s-lock-open')
                    ->offIcon('heroicon-s-lock-closed')
                    ->live()
                    ->dehydrated()
                    ->afterStateHydrated(function (Toggle $component, ?Role $record) use ($resourceKey): void {
                        $component->state($record instanceof Role && self::roleHasEveryResourcePermission($record, $resourceKey));
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state) use ($resourceKey): void {
                        foreach (AdminPermissionAction::cases() as $action) {
                            $set(self::permissionField($resourceKey, $action), $action !== AdminPermissionAction::ViewHis && (bool) $state);
                        }

                        self::syncSelectAllToggle($set, $get);
                    }),
                Fieldset::make(__('admin.Permissions'))
                    ->schema(self::permissionCheckboxes($resourceKey))
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'xl' => 3,
                    ])
                    ->extraAttributes([
                        'style' => 'border-color: rgb(var(--primary-500));',
                    ]),
            ])
            ->columns(1)
            ->columnSpan(1);
    }

    /**
     * @return array<int, Checkbox>
     */
    private static function permissionCheckboxes(string $resourceKey): array
    {
        return collect(AdminPermissionAction::cases())
            ->map(fn (AdminPermissionAction $action): Checkbox => Checkbox::make(self::permissionField($resourceKey, $action))
                ->label($action->label())
                ->live()
                ->dehydrated()
                ->afterStateHydrated(function (Checkbox $component, ?Role $record) use ($resourceKey, $action): void {
                    $component->state($record instanceof Role && self::roleHasPermission($record, $resourceKey, $action));
                })
                ->afterStateUpdated(function (Set $set, Get $get) use ($resourceKey, $action): void {
                    self::syncOppositeViewPermission($set, $get, $resourceKey, $action);
                    self::syncResourceToggle($set, $get, $resourceKey);
                    self::syncSelectAllToggle($set, $get);
                }))
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function resourceKeys(): array
    {
        return AdminPermissions::resourceKeys();
    }

    public static function resourceToggleField(string $resourceKey): string
    {
        return 'permission_resource__'.str_replace('-', '_', $resourceKey);
    }

    public static function permissionField(string $resourceKey, AdminPermissionAction $action): string
    {
        return 'permission_action__'.str_replace('-', '_', $resourceKey).'__'.$action->value;
    }

    /**
     * @return array<string>
     */
    public static function selectedPermissionNames(array $data): array
    {
        $permissionNames = [];

        foreach (self::resourceKeys() as $resourceKey => $label) {
            foreach (AdminPermissionAction::cases() as $action) {
                if (
                    $action === AdminPermissionAction::ViewAll
                    && (bool) ($data[self::permissionField($resourceKey, AdminPermissionAction::ViewHis)] ?? false)
                ) {
                    continue;
                }

                if (! (bool) ($data[self::permissionField($resourceKey, $action)] ?? false)) {
                    continue;
                }

                $permissionNames[] = AdminPermissions::permissionName($resourceKey, $action);
            }
        }

        return $permissionNames;
    }

    /**
     * @return array<string, mixed>
     */
    public static function stripPermissionFormState(array $data): array
    {
        foreach (array_keys($data) as $key) {
            if (str_starts_with((string) $key, 'permission_')) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public static function syncRolePermissions(Role $role, array $data): void
    {
        if (self::isAcademyTeacherSystemRole($role)) {
            return;
        }

        $permissionNames = self::selectedPermissionNames($data);

        $permissions = Permission::query()
            ->whereIn('name', $permissionNames)
            ->get();

        $role->syncPermissions($permissions);
    }

    private static function roleHasPermission(Role $role, string $resourceKey, AdminPermissionAction $action): bool
    {
        return $role
            ->permissions()
            ->where('name', AdminPermissions::permissionName($resourceKey, $action))
            ->exists();
    }

    private static function roleHasEveryResourcePermission(Role $role, string $resourceKey): bool
    {
        foreach (AdminPermissionAction::cases() as $action) {
            if ($action === AdminPermissionAction::ViewHis) {
                continue;
            }

            if (! self::roleHasPermission($role, $resourceKey, $action)) {
                return false;
            }
        }

        return true;
    }

    private static function roleHasAllPermissions(Role $role): bool
    {
        foreach (self::resourceKeys() as $resourceKey => $label) {
            if (! self::roleHasEveryResourcePermission($role, $resourceKey)) {
                return false;
            }
        }

        return filled(self::resourceKeys());
    }

    private static function syncResourceToggle(Set $set, Get $get, string $resourceKey): void
    {
        $hasEveryPermission = true;

        foreach (AdminPermissionAction::cases() as $action) {
            if ($action === AdminPermissionAction::ViewHis) {
                continue;
            }

            if (! (bool) $get(self::permissionField($resourceKey, $action))) {
                $hasEveryPermission = false;

                break;
            }
        }

        $set(self::resourceToggleField($resourceKey), $hasEveryPermission);
    }

    private static function syncOppositeViewPermission(Set $set, Get $get, string $resourceKey, AdminPermissionAction $action): void
    {
        if ($action === AdminPermissionAction::ViewAll && (bool) $get(self::permissionField($resourceKey, AdminPermissionAction::ViewAll))) {
            $set(self::permissionField($resourceKey, AdminPermissionAction::ViewHis), false);
        }

        if ($action === AdminPermissionAction::ViewHis && (bool) $get(self::permissionField($resourceKey, AdminPermissionAction::ViewHis))) {
            $set(self::permissionField($resourceKey, AdminPermissionAction::ViewAll), false);
        }
    }

    private static function syncSelectAllToggle(Set $set, Get $get): void
    {
        $hasEveryResource = true;

        foreach (self::resourceKeys() as $resourceKey => $label) {
            if (! (bool) $get(self::resourceToggleField($resourceKey))) {
                $hasEveryResource = false;

                break;
            }
        }

        $set('permission_select_all', $hasEveryResource && filled(self::resourceKeys()));
    }
}
