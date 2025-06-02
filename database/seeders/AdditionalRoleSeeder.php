<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdditionalRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sfpcoordinator = Role::firstOrCreate(['name' => 'sfp coordinator',
                            'guard_name' => 'web']);

        $sfpcoordinator->givePermissionTo([
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
    }
}
