<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleAccountsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Assistant Account
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Assistant',
                'password' => Hash::make('Admin123!'),
                'role' => 'super_admin',
                'position' => 'Administrative Assistant',
                'salary' => 25000.00,
            ]
        );

        // Create Cashier Account (separate from employee)
        User::updateOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Cashier User',
                'password' => Hash::make('Cashier123!'),
                'role' => 'cashier',
                'position' => 'Cashier',
                'salary' => 16000.00,
            ]
        );

        // Create Employee Accounts (regular employees)
        User::updateOrCreate(
            ['email' => 'employee1@example.com'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('Employee123!'),
                'role' => 'employee',
                'position' => 'Sales Clerk',
                'salary' => 18000.00,
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee2@example.com'],
            [
                'name' => 'Jane Employee',
                'password' => Hash::make('Employee123!'),
                'role' => 'employee',
                'position' => 'Sales Clerk',
                'salary' => 18000.00,
            ]
        );

        // Create Manager Account
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('Manager123!'),
                'role' => 'manager',
                'position' => 'Store Manager',
                'salary' => 35000.00,
            ]
        );

        $this->command->info('Sample accounts created successfully!');
    }
}
