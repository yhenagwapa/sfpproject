<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-child',
            'edit-child',
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

        $admin->givePermissionTo([
            'edit-child',
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
            'view-report',
            'print-report',
            'register',
            'edit-user-profile',
            'view-cycle-implementation',
            'create-child-development-center',
            'edit-child-development-center',
        ]);
    }
}