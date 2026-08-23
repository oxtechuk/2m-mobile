<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Retrieve a global setting value from database or cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null) {
        try {
            // Cache settings to prevent redundant DB calls
            return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('selected_branch_id')) {
    /**
     * Retrieve the currently active branch ID from session, fallback to auth user's branch
     *
     * @return int|string
     */
    function selected_branch_id() {
        if (session()->has('active_branch_id')) {
            $sessId = session('active_branch_id');
            if ($sessId === 'all') {
                return 'all';
            }
            return (int) $sessId;
        }
        
        return auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
    }
}
