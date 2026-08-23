<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $branchesCount = Branch::count();
        $branches = Branch::all();

        return view('settings.index', compact('settings', 'branchesCount', 'branches'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:255',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'receipt_footer' => 'nullable|string|max:255',
            'default_language' => 'required|in:ar,en',
            'default_currency' => 'required|string|max:10',
            'theme_color' => 'required|in:dark,light',
            'auto_print_receipt' => 'required|in:0,1',
            'auto_checkout_on_barcode' => 'nullable|in:0,1',
            'store_logo' => 'nullable|image|max:2048', // max 2MB
            'default_product_image' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Remove files from array to handle separately
        $logoFile = $request->file('store_logo');
        $defaultProductImageFile = $request->file('default_product_image');
        unset($validated['store_logo'], $validated['default_product_image']);

        // Save standard text settings
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            Cache::forget("setting_{$key}");
        }

        // Handle logo file upload
        if ($logoFile) {
            $path = $logoFile->store('uploads/logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'store_logo'],
                ['value' => $path]
            );
            Cache::forget("setting_store_logo");

            // Synchronize logo with PWA icons, apple-touch-icon, and favicon
            try {
                $fullLogoPath = storage_path('app/public/' . $path);
                if (file_exists($fullLogoPath)) {
                    if (!file_exists(public_path('icons'))) {
                        mkdir(public_path('icons'), 0755, true);
                    }
                    copy($fullLogoPath, public_path('icons/icon-192.png'));
                    copy($fullLogoPath, public_path('icons/icon-512.png'));
                    copy($fullLogoPath, public_path('apple-touch-icon.png'));
                    copy($fullLogoPath, public_path('favicon.png'));
                }
            } catch (\Exception $e) {
                // Ignore file system copy errors
            }
        }

        // Handle default product image upload
        if ($defaultProductImageFile) {
            $path = $defaultProductImageFile->store('uploads/products', 'public');
            Setting::updateOrCreate(
                ['key' => 'default_product_image'],
                ['value' => $path]
            );
            Cache::forget("setting_default_product_image");
        }

        flash('تم حفظ وتحديث إعدادات النظام والصورة الافتراضية بنجاح.')->success();

        return redirect()->route('settings.index');
    }
}
