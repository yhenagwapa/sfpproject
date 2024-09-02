<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::create(['name' => 'admin',
                            'guard_name' => 'web']);

        $cdw = Role::create(['name' => 'child development worker',
                            'guard_name' => 'web']);

        $admin->givePermissionTo([
            'edit-child',
            'delete-child',
            'view-report',
            'print-report',
            'create-child-development-center',
            'edit-child-development-center',
            'delete-child-development-center',
            'create-role',
            'edit-role',
            'delete-role',
            'register',
            'edit-user-profile',
            'delete-user',
            'view-audit-logs'
        ]);

        $cdw->givePermissionTo([
            'create-child',
            'add-attendance',
            'nutrition-status-entry',
            'nutrition-status-exit',
            'view-report',
            'print-report',
            'register',
            'edit-user-profile',
        ]);
    }
    
}