<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phones = Category::firstOrCreate(
            ['name' => 'هواتف ذكية'],
            [
                'icon' => 'fa-mobile-screen-button',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $tablets = Category::firstOrCreate(
            ['name' => 'أجهزة لوحية'],
            [
                'icon' => 'fa-tablet-screen-button',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $accessories = Category::firstOrCreate(
            ['name' => 'إكسسوارات'],
            [
                'icon' => 'fa-headphones',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $spareParts = Category::firstOrCreate(
            ['name' => 'قطع غيار صيانة'],
            [
                'icon' => 'fa-screwdriver-wrench',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        // Subcategories
        Category::firstOrCreate(
            ['name' => 'سماعات'],
            [
                'parent_id' => $accessories->id,
                'icon' => 'fa-headphones',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Category::firstOrCreate(
            ['name' => 'شواحن وكابلات'],
            [
                'parent_id' => $accessories->id,
                'icon' => 'fa-plug',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        Category::firstOrCreate(
            ['name' => 'شاشات موبايل'],
            [
                'parent_id' => $spareParts->id,
                'icon' => 'fa-mobile-screen',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Category::firstOrCreate(
            ['name' => 'بطاريات موبايل'],
            [
                'parent_id' => $spareParts->id,
                'icon' => 'fa-battery-three-quarters',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );
    }
}
