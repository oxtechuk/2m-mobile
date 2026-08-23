<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::firstOrCreate(
            ['name' => 'الفرع الرئيسي'],
            [
                'address' => 'وسط البلد، القاهرة',
                'phone' => '01011111111',
                'is_active' => true,
                'opening_balance' => 50000.00,
            ]
        );

        Branch::firstOrCreate(
            ['name' => 'فرع المعادي'],
            [
                'address' => 'شارع 9، المعادي، القاهرة',
                'phone' => '01022222222',
                'is_active' => true,
                'opening_balance' => 20000.00,
            ]
        );

        Branch::firstOrCreate(
            ['name' => 'فرع مدينة نصر'],
            [
                'address' => 'شارع عباس العقاد، مدينة نصر، القاهرة',
                'phone' => '01033333333',
                'is_active' => true,
                'opening_balance' => 15000.00,
            ]
        );
    }
}
