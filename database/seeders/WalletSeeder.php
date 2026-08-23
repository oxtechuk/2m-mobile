<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. General Main Wallet (Admin / General headquarters)
        Wallet::firstOrCreate(
            ['name' => 'المحفظة الرئيسية (الإدارة)'],
            [
                'type' => 'main',
                'branch_id' => null,
                'user_id' => null,
                'balance' => 100000.00,
                'is_active' => true,
            ]
        );

        // 2. Branch Wallets
        $branches = Branch::all();
        foreach ($branches as $branch) {
            Wallet::firstOrCreate(
                ['name' => 'خزينة فرع - ' . $branch->name],
                [
                    'type' => 'branch',
                    'branch_id' => $branch->id,
                    'user_id' => null,
                    'balance' => $branch->opening_balance,
                    'is_active' => true,
                ]
            );
        }

        // 3. Cashier/Employee Wallets
        $cashier = User::where('role', 'cashier')->first();
        if ($cashier) {
            Wallet::firstOrCreate(
                ['name' => 'خزينة عهدة الكاشير - ' . $cashier->name],
                [
                    'type' => 'employee',
                    'branch_id' => $cashier->branch_id,
                    'user_id' => $cashier->id,
                    'balance' => 0.00,
                    'is_active' => true,
                ]
            );
        }
    }
}
