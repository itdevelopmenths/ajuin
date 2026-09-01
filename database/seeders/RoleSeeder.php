<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $roles = [
            'Super Admin' => PermissionSeeder::PERMISSIONS,
            'SPV'         => [
                'ticket.view',
                'ticket.update_status',
                'ticket.export',
                'report.view',
            ],
            'Keptok'      => [
                'ticket.view',
                'ticket.create',
                'ticket.update_status',
                'ticket.export',
                'report.view',
            ],
            'HRGA'        => [
                'ticket.view',
                'ticket.update_status',
                'ticket.export',
                'report.view',
            ],
            'Chief'       => [
                'ticket.view',
                'ticket.update_status',
                'ticket.export',
                'report.view',
            ],
            'Manager'     => [
                'ticket.view',
                'report.view',
                'user.view',
                'store.view',
                'maintenance_type.view',
                'role.view',
            ],
        ];

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($roles as $name => $permissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($permissions);
        }

        // Hapus role lama yang tidak dipakai
        Role::whereNotIn('name', array_keys($roles))->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
