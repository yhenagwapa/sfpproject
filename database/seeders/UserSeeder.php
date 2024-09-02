<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'firstname' => 'admin',
            'lastname' => 'admin',
            'contact_no' => '123',
            'address' => 'Suazo St.',
            'zip_code' => '8000',
            'password' => Hash::make('dswd12345'),
            'email' => 'admin@admin.com',
            
        ]);
        $admin->assignRole('admin');

        // Creating Product Manager User
        $cdw = User::create([
            'firstname' => 'child development worker',
            'lastname' => 'child development worker',
            'contact_no' => '12345678901',
            'address' => 'Suazo St.',
            'zip_code' => '8000',
            'password' => Hash::make('dswd12345'),
            'email' => 'cdw@cdw.com',
        ]);
        $cdw->assignRole('child development worker');
    }
}
