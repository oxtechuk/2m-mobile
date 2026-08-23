<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin (Universal / No specific branch)
        $admin = User::firstOrCreate(
            ['email' => 'admin@2m.com'],
            [
                'name' => 'المدير العام',
                'phone' => '01000000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'branch_id' => null,
            ]
        );
        $admin->assignRole('admin');

        // Fetch branches
        $branches = Branch::all();

        if ($branches->count() > 0) {
            $branch1 = $branches->first();

            // Branch Manager
            $manager = User::firstOrCreate(
                ['email' => 'manager1@2m.com'],
                [
                    'name' => 'مدير الفرع الرئيسي',
                    'phone' => '01044444444',
                    'password' => Hash::make('password'),
                    'role' => 'branch_manager',
                    'is_active' => true,
                    'branch_id' => $branch1->id,
                    'salary' => 8000.00,
                ]
            );
            $manager->assignRole('branch_manager');

            // Update branch manager_id
            $branch1->update(['manager_id' => $manager->id]);

            // Cashier
            $cashier = User::firstOrCreate(
                ['email' => 'cashier1@2m.com'],
                [
                    'name' => 'كاشير الفرع الرئيسي',
                    'phone' => '01055555555',
                    'password' => Hash::make('password'),
                    'role' => 'cashier',
                    'is_active' => true,
                    'branch_id' => $branch1->id,
                    'salary' => 4500.00,
                ]
            );
            $cashier->assignRole('cashier');

            // Technician
            $tech = User::firstOrCreate(
                ['email' => 'tech1@2m.com'],
                [
                    'name' => 'فني صيانة رئيسي',
                    'phone' => '01066666666',
                    'password' => Hash::make('password'),
                    'role' => 'technician',
                    'is_active' => true,
                    'branch_id' => $branch1->id,
                    'salary' => 6000.00,
                    'commission_rate' => 10.00, // 10% commission on repairs
                ]
            );
            $tech->assignRole('technician');

            // Customer Service
            $cs = User::firstOrCreate(
                ['email' => 'cs1@2m.com'],
                [
                    'name' => 'خدمة عملاء رئيسي',
                    'phone' => '01077777777',
                    'password' => Hash::make('password'),
                    'role' => 'customer_service',
                    'is_active' => true,
                    'branch_id' => $branch1->id,
                    'salary' => 4000.00,
                ]
            );
            $cs->assignRole('customer_service');
        }
    }
}
