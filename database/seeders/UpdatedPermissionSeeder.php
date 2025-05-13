<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdatedPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-child',
            'edit-child',
            'view-child',
            'add-attendance',
            'create-nutritional-status',
            'edit-nutritional-status',
            'view-report',
            'print-report',
            'create-child-development-center',
            'edit-child-development-center',
            'create-role',
            'edit-role',
            'register',
            'edit-user-profile',
            'view-audit-logs',
            'add-cycle-implementation',
            'edit-cycle-implementation',
            'view-cycle-implementation'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        $admin = Role::firstOrCreate(['name' => 'admin',
            'guard_name' => 'web']);

        $cdw = Role::firstOrCreate(['name' => 'child development worker',
            'guard_name' => 'web']);

        $lguFocal= Role::firstOrCreate(['name' => 'lgu focal',
            'guard_name' => 'web']);

        $encoder= Role::firstOrCreate(['name' => 'encoder',
            'guard_name' => 'web']);

        $pdo= Role::firstOrCreate(['name' => 'pdo',
            'guard_name' => 'web']);

        $admin->givePermissionTo([
            'edit-child',
            'view-child',
            'edit-nutritional-status',
            'view-report',
            'print-report',
            'edit-child-development-center',
            'create-role',
            'edit-role',
            'register',
            'edit-user-profile',
            'view-audit-logs',
            'add-cycle-implementation',
            'edit-cycle-implementation',
            'view-cycle-implementation'
        ]);

        $cdw->givePermissionTo([
            'create-child',
            'view-child',
            'add-attendance',
            'create-nutritional-status',
            'edit-nutritional-status',
            'view-report',
            'print-report',
            'register',
            'edit-user-profile',
            'view-cycle-implementation'
        ]);

        $lguFocal->givePermissionTo([
            'edit-child',
            'view-report',
            'view-child',
            'print-report',
            'register',
            'edit-user-profile',
            'view-cycle-implementation',
            'create-child-development-center',
            'edit-child-development-center',
        ]);

        $encoder->givePermissionTo([
            'create-child',
            'view-child',
            'create-nutritional-status',
            'edit-nutritional-status',
        ]);

        $pdo->givePermissionTo([
            'view-child',
        ]);
    }
}
