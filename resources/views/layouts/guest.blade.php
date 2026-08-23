<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '2M Mobile') }} - تسجيل الدخول</title>
        
        <!-- App & Browser Icons -->
        <link rel="icon" type="image/png" href="{{ setting('store_logo') ? asset('storage/' . setting('store_logo')) : asset('icons/icon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ setting('store_logo') ? asset('storage/' . setting('store_logo')) : asset('icons/icon-192.png') }}">

        <!-- Fonts & Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#050505] text-gray-100 font-sans antialiased overflow-hidden">
        <!-- Glowing background blobs -->
        <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-[#D41414]/15 rounded-full filter blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-1/4 left-1/4 w-96 h-96 bg-[#D41414]/5 rounded-full filter blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="min-h-screen flex flex-col justify-center items-center p-6 relative z-10">
            <!-- Brand Logo Header -->
            <div class="mb-6 flex flex-col items-center">
                @if(setting('store_logo'))
                    <img src="{{ asset('storage/' . setting('store_logo')) }}" class="w-16 h-16 rounded-2xl object-cover mb-3">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-[#D41414] flex items-center justify-center glow-primary mb-3">
                        <span class="text-white font-extrabold text-3xl">2M</span>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-white tracking-wide text-glow-primary">{{ setting('store_name', '2M Mobile') }}</h1>
                <p class="text-xs text-gray-400 mt-1">نظام المبيعات والصيانة المتكامل للفروع</p>
            </div>

            <!-- Login Glass Card -->
            <div class="w-full sm:max-w-md px-8 py-8 glass-panel shadow-2xl relative overflow-hidden">
                <!-- Top border accent line -->
                <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-transparent via-[#D41414] to-transparent"></div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
