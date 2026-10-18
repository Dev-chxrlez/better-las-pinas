<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['submit report','view own reports','view assigned reports','review incoming reports','assign reports','update report status','resolve assigned reports','manage users','manage categories','view analytics','view audit logs'];
        foreach ($permissions as $permission) Permission::findOrCreate($permission, 'web');
        Role::findOrCreate('resident', 'web')->syncPermissions(['submit report','view own reports']);
        Role::findOrCreate('intake-officer', 'web')->syncPermissions(['review incoming reports','assign reports']);
        Role::findOrCreate('barangay-officer', 'web')->syncPermissions(['view assigned reports','update report status','resolve assigned reports']);
        Role::findOrCreate('city-department-officer', 'web')->syncPermissions(['view assigned reports','update report status','resolve assigned reports']);
        Role::findOrCreate('system-administrator', 'web')->syncPermissions($permissions);
    }
}
