<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="{{ setting('theme_color', 'dark') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '2M Mobile') }}</title>

        <!-- Manifest & PWA Meta for Native Chrome Address Bar Install Icon -->
        <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
        <meta name="theme-color" content="#D41414">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ setting('store_name', '2M Mobile') }}">
        
        <!-- App & Browser Icons -->
        <link rel="icon" type="image/png" href="{{ setting('store_logo') ? asset('storage/' . setting('store_logo')) : asset('icons/icon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ setting('store_logo') ? asset('storage/' . setting('store_logo')) : asset('icons/icon-192.png') }}">

        <!-- Fonts & Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#050505] text-gray-100 font-sans antialiased overflow-x-hidden">
        <div class="min-h-screen flex" x-data="{ sidebarOpen: true, moreMenuOpen: false }">
            
            <!-- Right Sidebar Navigation -->
            @include('layouts.partials.sidebar')

            <!-- Main Layout Window -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
                
                <!-- Top Navbar -->
                @include('layouts.partials.navbar')

                <!-- Main Content Area -->
                <main class="flex-1 p-6">
                    <!-- Flash Messages -->
                    @if (session()->has('flash_notification'))
                        <div class="mb-4">
                            @include('flash::message')
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="py-4 px-6 bg-[#121212] border-t border-white/5 text-center text-xs text-gray-500 flex flex-wrap items-center justify-between gap-2">
                    <span>&copy; {{ date('Y') }} {{ setting('store_name', '2M Mobile') }} - جميع الحقوق محفوظة.</span>
                    <span>برمجة وتطوير شركة <a href="https://oxtech.uk" target="_blank" class="text-gray-400 hover:text-white font-bold transition underline decoration-[#D41414]">Ox Tech</a> | <a href="https://oxtech.uk" target="_blank" class="text-[#D41414] font-mono font-bold hover:underline">oxtech.uk</a></span>
                </footer>
            </div>
        </div>

        <!-- Sticky Bottom Navigation for Mobile -->
        @include('layouts.partials.bottom_nav')

        <!-- Mobile More Menu Bottom Sheet Drawer -->
        <div 
            x-show="moreMenuOpen" 
            x-transition:opacity
            class="fixed inset-0 bg-[#050505]/80 backdrop-blur-sm z-50 md:hidden"
            style="display: none;"
            @click="moreMenuOpen = false"
        >
            <div 
                x-show="moreMenuOpen"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="fixed bottom-0 left-0 right-0 max-h-[85vh] bg-[#121212] border-t border-white/10 rounded-t-3xl p-6 overflow-y-auto space-y-6"
                @click.stop
            >
                <!-- Drawer Header -->
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center space-x-2 space-x-reverse">
                        <i class="fa-solid fa-grid-2 text-[#D41414]"></i>
                        <span>قائمة المزيد من الخيارات</span>
                    </h3>
                    <button @click="moreMenuOpen = false" class="text-gray-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Grid of Option Links (Mobile friendly blocks) -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    
                    @if(auth()->user()->can('manage-customers') || auth()->user()->can('view-customers'))
                    <a href="{{ route('customers.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-users-gear text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">العملاء</span>
                    </a>
                    @endif

                    @can('manage-products')
                    <a href="{{ route('products.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-mobile-screen text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">المنتجات والأجهزة</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-tags text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">التصنيفات</span>
                    </a>
                    @endcan

                    @can('manage-inventory')
                    <a href="{{ route('inventory.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-boxes-stacked text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">المخزن</span>
                    </a>
                    @endcan

                    @can('manage-sales')
                    <a href="{{ route('sales.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-file-invoice-dollar text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">المبيعات والفواتير</span>
                    </a>
                    @endcan

                    @if(auth()->user()->can('manage-wallets') || auth()->user()->role === 'branch_manager')
                    <a href="{{ route('wallets.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-wallet text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">الخزائن والمحافظ</span>
                    </a>
                    @endif

                    @can('manage-transfers')
                    <a href="{{ route('transfers.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-money-bill-transfer text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">التحويلات</span>
                    </a>
                    @endcan

                    @can('manage-expenses')
                    <a href="{{ route('expenses.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-circle-minus text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">المصروفات</span>
                    </a>
                    @endcan

                    @if(auth()->user()->can('view-reports') || auth()->user()->can('view-branch-reports'))
                    <a href="{{ route('reports.sales') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-chart-pie text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">التقارير</span>
                    </a>
                    @endif

                    @can('manage-settings')
                    <a href="{{ route('settings.index') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-sliders text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">الإعدادات</span>
                    </a>
                    @endcan

                    <a href="{{ route('profile.edit') }}" class="p-3 bg-white/5 border border-white/5 rounded-xl flex flex-col items-center gap-2 hover:bg-white/10 transition">
                        <i class="fa-solid fa-user-gear text-lg text-[#D41414]"></i>
                        <span class="text-xs text-white">الملف الشخصي</span>
                    </a>
                </div>

                <!-- Log Out Button -->
                <div class="pt-3 border-t border-white/5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold rounded-xl transition text-xs">
                            تسجيل الخروج <i class="fa-solid fa-right-from-bracket mr-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        <script>
            function downloadDesktopShortcutFile() {
                const targetUrl = window.location.origin + '/pos';
                const shortcutContent = `[InternetShortcut]\nURL=${targetUrl}\nIDList=\nHotKey=0\nIconIndex=0\n`;
                const blob = new Blob([shortcutContent], { type: 'application/x-mswinurl' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = '2M Mobile - POS.url';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(a.href);
            }

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('2M PWA ServiceWorker Registered:', reg.scope))
                        .catch(err => console.log('ServiceWorker registration failed:', err));
                });
            }
        </script>
    </body>
</html>
