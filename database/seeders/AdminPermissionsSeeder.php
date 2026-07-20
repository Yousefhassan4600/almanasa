<?php

// php artisan db:seed --class=AdminPermissionsSeeder

namespace Database\Seeders;

use App\Models\AcademyTeacher;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(AdminPermissions::permissionOptions()) as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        AcademyTeacher::academyTeacherRole();

        AcademyTeacher::query()
            ->with('teacher.owner')
            ->where('is_active', true)
            ->each(fn (AcademyTeacher $academyTeacher): mixed => $academyTeacher->syncSpatieRole());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
