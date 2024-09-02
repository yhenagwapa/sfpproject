<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

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
            'delete-child',
            'add-attendance',
            'nutrition-status-entry',
            'nutrition-status-exit',
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
         ];
 
          // Looping and Inserting Array's Permissions into Permission Table
         foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
          }
    }
}