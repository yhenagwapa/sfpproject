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
        if (!User::where('email', 'admin@admin.com')->exists()) {
            $admin = User::create([
                'firstname' => 'admin',
                'lastname' => 'admin',
                'contact_number' => '123',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'admin@admin.com',
                'status' => 'active',
            ]);
            $admin->assignRole('admin');
        }

        // Check if 'child development worker' user already exists
        if (!User::where('email', 'cdw@cdw.com')->exists()) {
            $cdw = User::create([
                'firstname' => 'child',
                'middlename' => 'development',
                'lastname' => 'worker',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'cdw@cdw.com',
                'status' => 'active',
            ]);
            $cdw->assignRole('child development worker');
        }

        // Check if 'lgu focal' user already exists
        if (!User::where('email', 'focal@focal.com')->exists()) {
            $lguFocal = User::create([
                'firstname' => 'lgu',
                'lastname' => 'focal',
                'contact_number' => '123',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'focal@focal.com',
                'status' => 'active',
            ]);
            $lguFocal->assignRole('lgu focal');
        }

        // Check if 'test' user already exists
        if (!User::where('email', 'test@test.com')->exists()) {
            $test = User::create([
                'firstname' => 'test',
                'lastname' => 'test',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'test@test.com',
                'status' => 'active',
            ]);
            $test->assignRole('child development worker');
        }

        // Check if 'focal' user already exists
        if (!User::where('email', 'focal2@test.com')->exists()) {
            $focal = User::create([
                'firstname' => 'focal 2',
                'lastname' => 'test',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'focal2@test.com',
                'status' => 'active',
            ]);
            $focal->assignRole('lgu focal');
        }

        // Check if 'encoder' user already exists
        if (!User::where('email', 'encoder@encoder.com')->exists()) {
            $encoder = User::create([
                'firstname' => 'encoder',
                'lastname' => 'encoder',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'encoder@encoder.com',
                'status' => 'active',
            ]);
            $encoder->assignRole('encoder');
        }

        if (!User::where('email', 'encoder2@encoder.com')->exists()) {
            $encoder2 = User::create([
                'firstname' => 'encoder2',
                'lastname' => 'encoder2',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'encoder2@encoder.com',
                'status' => 'active',
            ]);
            $encoder2->assignRole('encoder');
        }

        // Check if 'pdo' user already exists
        if (!User::where('email', 'pdo@pdo.com')->exists()) {
            $pdo = User::create([
                'firstname' => 'pdo',
                'lastname' => 'pdo',
                'contact_number' => '12345678901',
                'address' => 'Suazo St.',
                'password' => Hash::make('dswd12345'),
                'email' => 'pdo@pdo.com',
                'status' => 'active',
            ]);
            $pdo->assignRole('pdo');
        }
    }
}
