<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        'ticket.view',
        'ticket.create',
        'ticket.update_status',
        'ticket.delete',
        'ticket.export',
        'report.view',
        'report.export',
        'user.view',
        'user.create',
        'user.edit',
        'user.delete',
        'store.view',
        'store.manage',
        'maintenance_type.view',
        'maintenance_type.manage',
        'role.view',
        'role.manage',
        'scope.manage',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
