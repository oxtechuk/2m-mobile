<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'group' => 'general',
                'key' => 'store_name',
                'value' => '2M Mobile',
                'type' => 'text',
            ],
            [
                'group' => 'general',
                'key' => 'tax_rate',
                'value' => '14',
                'type' => 'number',
            ],
            [
                'group' => 'general',
                'key' => 'currency',
                'value' => 'EGP',
                'type' => 'text',
            ],
            [
                'group' => 'maintenance',
                'key' => 'terms_and_conditions',
                'value' => 'شروط الصيانة: 1. الفحص الفني يستغرق 24-48 ساعة. 2. الضمان لا يشمل الكسر والسوائل. 3. يسقط حق العميل في المطالبة بالجهاز بعد مرور 3 أشهر من تاريخ الجاهزية للتسليم.',
                'type' => 'text',
            ],
            [
                'group' => 'pos',
                'key' => 'allow_negative_inventory',
                'value' => 'false',
                'type' => 'boolean',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}
