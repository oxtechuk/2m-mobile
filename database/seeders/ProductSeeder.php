<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phoneCat = Category::where('name', 'هواتف ذكية')->first();
        $chargerCat = Category::where('name', 'شواحن وكابلات')->first();
        $screenCat = Category::where('name', 'شاشات موبايل')->first();

        $branches = Branch::all();

        // 1. Phone Product (has_serials = true)
        $iphone = Product::firstOrCreate(
            ['sku' => 'IPH15PRO-128'],
            [
                'name' => 'iPhone 15 Pro 128GB Black',
                'barcode' => '190198123456',
                'category_id' => $phoneCat->id,
                'description' => 'Apple iPhone 15 Pro with Titanium design, A17 Pro chip.',
                'cost_price' => 45000.00,
                'selling_price' => 52000.00,
                'wholesale_price' => 49500.00,
                'minimum_stock' => 2,
                'unit' => 'piece',
                'has_serials' => true,
                'is_active' => true,
            ]
        );

        $samsung = Product::firstOrCreate(
            ['sku' => 'SAMS24U-256'],
            [
                'name' => 'Samsung Galaxy S24 Ultra 256GB Gray',
                'barcode' => '880609123456',
                'category_id' => $phoneCat->id,
                'description' => 'Samsung flagship with AI capabilities, Snapdragon 8 Gen 3.',
                'cost_price' => 48000.00,
                'selling_price' => 55000.00,
                'wholesale_price' => 52500.00,
                'minimum_stock' => 2,
                'unit' => 'piece',
                'has_serials' => true,
                'is_active' => true,
            ]
        );

        // 2. Accessory Product (has_serials = false)
        $ankerCharger = Product::firstOrCreate(
            ['sku' => 'ANK-CHARG-30W'],
            [
                'name' => 'Anker Nano II 30W Charger USB-C',
                'barcode' => '848061054321',
                'category_id' => $chargerCat->id,
                'description' => 'Fast charger plug for iPhone and Android.',
                'cost_price' => 450.00,
                'selling_price' => 650.00,
                'wholesale_price' => 550.00,
                'minimum_stock' => 5,
                'unit' => 'piece',
                'has_serials' => false,
                'is_active' => true,
            ]
        );

        // 3. Spare Part Product (has_serials = false)
        $iphone11Screen = Product::firstOrCreate(
            ['sku' => 'SCR-IPH11-ORG'],
            [
                'name' => 'شاشة آيفون 11 أصلية توكيل',
                'barcode' => '200011000123',
                'category_id' => $screenCat->id,
                'description' => 'Original replacement screen for Apple iPhone 11.',
                'cost_price' => 2200.00,
                'selling_price' => 3200.00,
                'wholesale_price' => 2600.00,
                'minimum_stock' => 3,
                'unit' => 'piece',
                'has_serials' => false,
                'is_active' => true,
            ]
        );

        // Populate inventories & serials across all branches
        foreach ($branches as $branch) {
            // For accessories & spare parts, add bulk inventory
            Inventory::updateOrCreate(
                ['product_id' => $ankerCharger->id, 'branch_id' => $branch->id],
                ['quantity' => 15, 'reserved_quantity' => 0]
            );

            Inventory::updateOrCreate(
                ['product_id' => $iphone11Screen->id, 'branch_id' => $branch->id],
                ['quantity' => 8, 'reserved_quantity' => 0]
            );

            // For iPhone (with serials)
            $iphoneSerials = [
                'IMEI-IPH15-' . $branch->id . '-1',
                'IMEI-IPH15-' . $branch->id . '-2',
                'IMEI-IPH15-' . $branch->id . '-3',
            ];
            foreach ($iphoneSerials as $imei) {
                ProductSerial::updateOrCreate(
                    ['serial_number' => $imei],
                    [
                        'product_id' => $iphone->id,
                        'branch_id' => $branch->id,
                        'status' => 'in_stock',
                    ]
                );
            }
            Inventory::updateOrCreate(
                ['product_id' => $iphone->id, 'branch_id' => $branch->id],
                ['quantity' => count($iphoneSerials), 'reserved_quantity' => 0]
            );

            // For Samsung (with serials)
            $samsungSerials = [
                'IMEI-SAMS24-' . $branch->id . '-1',
                'IMEI-SAMS24-' . $branch->id . '-2',
            ];
            foreach ($samsungSerials as $imei) {
                ProductSerial::updateOrCreate(
                    ['serial_number' => $imei],
                    [
                        'product_id' => $samsung->id,
                        'branch_id' => $branch->id,
                        'status' => 'in_stock',
                    ]
                );
            }
            Inventory::updateOrCreate(
                ['product_id' => $samsung->id, 'branch_id' => $branch->id],
                ['quantity' => count($samsungSerials), 'reserved_quantity' => 0]
            );
        }
    }
}
