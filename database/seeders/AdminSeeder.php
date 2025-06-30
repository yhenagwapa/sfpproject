<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'yvvillamil@dswd.gov.ph')->exists()) {
            $admin = User::create([
                'firstname' => 'yhena',
                'lastname' => 'villamil',
                'contact_number' => '09173010422',
                'address' => 'Suazo St.',
                'psgc_id' => '368',
                'password' => Hash::make('dswd12345'),
                'email' => 'yvvillamil@dswd.gov.ph',
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
            $admin->assignRole('admin');
        }
    }
}
