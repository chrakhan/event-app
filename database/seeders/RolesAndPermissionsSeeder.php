<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::firstOrCreate(['name' => 'event.view']);
        Permission::firstOrCreate(['name' => 'event.create']);
        Permission::firstOrCreate(['name' => 'event.edit']);
        Permission::firstOrCreate(['name' => 'event.delete']);

        // Create roles
        $admin     = Role::firstOrCreate(['name' => 'admin']);
        $organizer = Role::firstOrCreate(['name' => 'organizer']);

        // Give organizer all 4 permissions
        $organizer->givePermissionTo([
            'event.view',
            'event.create',
            'event.edit',
            'event.delete',
        ]);

        // Give admin all permissions
        $admin->givePermissionTo(Permission::all());
    }
}
