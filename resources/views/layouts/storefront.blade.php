<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'تمورو | أدوات تعليمية تنمي مهارات طفلك')</title>

        <!-- Google Fonts: Cairo -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Meta Pixel Code -->
        @if(config('services.meta.pixel_id'))
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ config('services.meta.pixel_id') }}');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ config('services.meta.pixel_id') }}&ev=PageView&noscript=1"
        /></noscript>
        @endif

        <!-- Global site tag (gtag.js) - Google Analytics -->
        @if(config('services.google.analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
        @endif

        <style>
            body {
                font-family: 'Cairo', sans-serif;
                background-color: #FFFFFF;
                color: #102A63;
            }
            .brand-navy { color: #1E3A8A; }
            .brand-blue { color: #2563EB; }
            .brand-turquoise { color: #14B8A6; }
            .brand-coral { color: #F97376; }
            .bg-brand-navy { background-color: #1E3A8A; }
            .bg-brand-blue { background-color: #2563EB; }
            .bg-brand-turquoise { background-color: #14B8A6; }
            .bg-brand-coral { background-color: #F97376; }
            .border-brand-navy { border-color: #1E3A8A; }
            .border-brand-blue { border-color: #2563EB; }
            .border-brand-turquoise { border-color: #14B8A6; }
            .border-brand-coral { border-color: #F97376; }
        </style>
        @yield('styles')
    </head>
    <body class="antialiased" x-data="{ cartOpen: false }">
        
        <!-- 1. Top Announcement Bar -->
        <div class="bg-gray-100 text-xs py-2 px-4 border-b border-gray-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-2">
                <div class="flex items-center gap-4 text-slate-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        شحن مجاني للطلبات فوق 550 جنيه
                    </span>
                    <span class="hidden md:inline text-gray-300">|</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-brand-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2z"></path></svg>
                        خصومات تصل إلى 30% على الباقات
                    </span>
                </div>
                <div class="flex items-center gap-4 text-slate-600">
                    <a href="https://wa.me/201012345678" target="_blank" class="flex items-center gap-1 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4 text-green-500 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.458L0 24zm6.002-3.693c1.615.957 3.178 1.462 4.736 1.463 5.485.002 9.948-4.463 9.95-9.953.001-2.66-1.025-5.16-2.887-7.026C16.001 2.923 13.506 1.897 10.85 1.897c-5.486 0-9.949 4.464-9.953 9.954-.001 2.052.541 4.06 1.567 5.814l-1.026 3.75 3.829-1.004z"></path></svg>
                        واتساب وخدمة العملاء
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-brand-blue transition-colors">تنزيل التطبيق</a>
                </div>
            </div>
        </div>

        <!-- 2. Header: Logo, Search, Actions -->
        <header class="bg-white py-4 px-4 sticky top-0 z-50 shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex flex-col">
                        <div class="text-3xl font-extrabold tracking-tight flex items-center gap-1">
                            <span class="text-[#102A63]">2</span>
                            <span class="text-brand-blue">mor</span>
                            <span class="text-brand-coral">r</span>
                            <span class="text-[#102A63]">o</span>
                            <div class="w-2.5 h-2.5 rounded-full bg-brand-coral -mt-2"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 font-semibold mt-0.5">أدوات تعليمية .. تنمي مهارات طفلك</span>
                    </div>
                </a>

                <!-- Search Bar -->
                <div class="w-full md:max-w-2xl">
                    <form action="{{ route('search') }}" method="GET" class="relative flex items-center bg-slate-100 rounded-full overflow-hidden border border-gray-200 focus-within:border-brand-blue focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <div class="px-4 py-3 bg-slate-200 text-xs font-semibold text-slate-600 border-l border-gray-300 cursor-pointer flex items-center gap-1 select-none">
                            <span>كل الأقسام</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن منتج، شيت، أو مهارة..." class="w-full px-4 py-2.5 bg-transparent border-0 text-sm focus:ring-0 text-slate-700 placeholder-slate-400">
                        <button type="submit" class="p-3 text-brand-blue hover:text-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <!-- User actions -->
                <div class="flex items-center gap-6">
                    <!-- Documents / Downloads -->
                    <a href="#" class="flex flex-col items-center group text-slate-600 hover:text-brand-blue transition-colors">
                        <div class="relative">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold mt-1">المستندات</span>
                    </a>

                    <!-- Wishlist -->
                    <a href="#" class="flex flex-col items-center group text-slate-600 hover:text-red-500 transition-colors">
                        <div class="relative">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold mt-1">المفضلة</span>
                    </a>

                    <!-- Cart Button (opens drawer) -->
                    @php
                        $cart = session()->get('cart', []);
                        $cartCount = count($cart);
                        $cartTotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
                        $physicalCount = array_sum(array_map(fn($item) => $item['type'] === 'physical' ? $item['quantity'] : 0, $cart));
                    @endphp
                    <button @click.prevent="cartOpen = true" class="flex items-center gap-3 bg-blue-50 hover:bg-blue-100 py-1.5 px-3.5 rounded-full border border-blue-100 group transition-all select-none">
                        <div class="relative">
                            <svg class="w-6 h-6 text-brand-blue group-hover:scale-115 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-brand-coral text-white text-[10px] font-bold w-4.5 h-4.5 rounded-full flex items-center justify-center border border-white">
                                {{ $cartCount }}
                            </span>
                        </div>
                        <div class="flex flex-col items-start leading-tight">
                            <span class="text-[9px] text-slate-500 font-bold">السلة</span>
                            <span class="text-xs font-extrabold text-brand-blue">{{ number_format($cartTotal, 2) }} جنيه</span>
                        </div>
                    </button>
                </div>
            </div>
        </header>

        <!-- 3. Navigation Bar (Main Menu) -->
        <nav class="bg-[#102A63] text-white py-3.5 px-4 shadow-md sticky top-[80px] z-40">
            <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-4">
                <div class="flex flex-wrap items-center gap-8 text-sm font-bold">
                    <a href="{{ route('home') }}" class="hover:text-brand-coral transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        الرئيسية
                    </a>
                    <a href="{{ route('search', ['category' => 'educational-tools']) }}" class="hover:text-brand-coral transition-colors">الأدوات التعليمية</a>
                    <a href="{{ route('search', ['category' => 'courses']) }}" class="hover:text-brand-coral transition-colors">الكورسات والبرامج</a>
                    <a href="{{ route('search', ['category' => 'educational-bundles']) }}" class="hover:text-brand-coral transition-colors">الباقات والعروض</a>
                    <a href="{{ route('search') }}" class="hover:text-brand-coral transition-colors">حسب المهارة</a>
                    <a href="#" class="hover:text-brand-coral transition-colors">حساب العمر</a>
                    <a href="#" class="hover:text-brand-coral transition-colors">تواصل معنا</a>
                </div>

                @if(Auth::check())
                    <div class="flex items-center gap-3 text-xs font-semibold">
                        <span>أهلاً، {{ Auth::user()->name }}</span>
                        <a href="{{ route('dashboard') }}" class="underline hover:text-brand-coral">لوحة الحساب</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-brand-coral">تسجيل خروج</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-1 bg-[#1e3264] hover:bg-[#253c7a] py-1 px-4 rounded-full text-xs font-bold transition-all border border-blue-800">
                        <svg class="w-4 h-4 text-brand-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        دخول / تسجيل
                    </a>
                @endif
            </div>
        </nav>

        <!-- Notification Banner (Global alerts) -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div class="bg-green-50 border border-green-200 text-green-700 text-xs font-bold p-4 rounded-xl">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- 4. Main View Content -->
        <main>
            @yield('content')
        </main>

        <!-- 5. Interactive Slide-Over Cart Drawer -->
        <div class="fixed inset-0 overflow-hidden z-[100]" style="display: none;" x-show="cartOpen" x-transition>
            <div class="absolute inset-0 overflow-hidden">
                <!-- Background blur overlay -->
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="cartOpen = false"></div>

                <div class="fixed inset-y-0 left-0 pl-10 max-w-full flex">
                    <!-- Drawer Content Container -->
                    <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between"
                         @click.away="cartOpen = false"
                         x-show="cartOpen"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-300"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full">
                        
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                🛒 سلة المشتريات
                            </h3>
                            <button @click="cartOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                        </div>

                        <!-- Items List -->
                        <div class="flex-grow overflow-y-auto p-6 flex flex-col gap-4">
                            @if(empty($cart))
                                <div class="flex flex-col items-center justify-center gap-4 text-center my-auto">
                                    <span class="text-4xl">🛍️</span>
                                    <h4 class="text-xs font-bold text-slate-600">السلة فارغة حالياً</h4>
                                    <p class="text-[10px] text-slate-400 max-w-xs">تصفح الأدوات التعليمية والشيتات وأضفها إلى سلتك لبدء التعلم.</p>
                                    <button @click="cartOpen = false" class="bg-[#102A63] text-white text-xs font-bold py-2 px-6 rounded-full mt-2">تصفح المنتجات</button>
                                </div>
                            @else
                                @foreach($cart as $id => $item)
                                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 last:border-0">
                                        <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden p-1 flex-shrink-0 flex items-center justify-center">
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-contain">
                                            @else
                                                📄
                                            @endif
                                        </div>
                                        <div class="flex-grow leading-tight">
                                            <h4 class="text-xs font-bold text-slate-700 limit-lines-1">{{ $item['name'] }}</h4>
                                            <span class="text-[10px] text-slate-400 font-semibold mt-1">{{ number_format($item['price'], 2) }} ج.م • {{ $item['type'] === 'digital' ? 'شيت PDF' : 'مادي' }}</span>
                                            
                                            <!-- Quantity selector / remove -->
                                            <div class="flex items-center gap-3 mt-2">
                                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center border border-slate-200 rounded-lg overflow-hidden text-xs">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="px-2 py-0.5 hover:bg-slate-100">-</button>
                                                    <span class="px-3 py-0.5 bg-slate-50 font-bold">{{ $item['quantity'] }}</span>
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="px-2 py-0.5 hover:bg-slate-100">+</button>
                                                </form>

                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                                    <button type="submit" class="text-[10px] text-red-500 hover:underline">حذف</button>
                                                </form>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-800 flex-shrink-0">{{ number_format($item['price'] * $item['quantity'], 2) }} ج.م</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Footer / Totals (If not empty) -->
                        @if(!empty($cart))
                            <div class="p-6 border-t border-slate-200 bg-slate-50 flex flex-col gap-4">
                                <!-- Free shipping target -->
                                @if($physicalCount > 0 && $cartTotal < 550)
                                    <div class="flex flex-col gap-1 text-[10px] font-extrabold text-brand-blue">
                                        <span>متبقي {{ number_format(550 - $cartTotal, 2) }} جنيه للحصول على شحن مجاني!</span>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden mt-1">
                                            <div class="bg-brand-blue h-full rounded-full" style="width: {{ ($cartTotal / 550) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @elseif($physicalCount > 0 && $cartTotal >= 550)
                                    <div class="text-[10px] font-black text-green-600 bg-green-50 p-2 rounded-lg border border-green-100 flex items-center justify-center gap-1">
                                        ✓ لقد حصلت على شحن مجاني لطلبك!
                                    </div>
                                @endif

                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span class="text-slate-500">المجموع الفرعي:</span>
                                    <span class="text-slate-800 text-sm font-black">{{ number_format($cartTotal, 2) }} ج.م</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full border border-slate-300 hover:bg-slate-100 text-slate-600 font-bold text-xs py-2.5 rounded-xl transition-colors">تفريغ السلة</button>
                                    </form>
                                    <a href="{{ route('checkout.index') }}" class="w-full bg-[#102A63] hover:bg-slate-800 text-white font-bold text-xs py-2.5 rounded-xl text-center flex items-center justify-center transition-colors">إتمام الشراء</a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Footer Section -->
        <footer class="bg-slate-50 border-t border-slate-200 mt-16 pt-16 pb-8 px-4">
            <div class="max-w-7xl mx-auto">
                
                <!-- Upper footer: newsletter and social -->
                <div class="grid grid-cols-1 lg:grid-cols-2 justify-between items-center gap-8 pb-12 border-b border-slate-200">
                    <div class="flex flex-col gap-2">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-5 bg-brand-blue rounded-full"></span>
                            اشترك في نشرتنا البريدية
                        </h4>
                        <p class="text-xs text-slate-500 font-semibold">احصل على أحدث العروض والملفات المحدثة والنصائح التعليمية دورياً.</p>
                        <form action="#" method="POST" class="mt-4 flex gap-2 max-w-md">
                            <input type="email" placeholder="أدخل بريدك الإلكتروني" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-blue focus:ring-1 focus:ring-blue-100">
                            <button type="submit" class="bg-brand-navy hover:bg-slate-800 text-white font-bold text-xs py-2 px-6 rounded-lg transition-colors whitespace-nowrap">اشترك الآن</button>
                        </form>
                    </div>
                    <div class="flex flex-col lg:items-end gap-3">
                        <h4 class="text-sm font-bold text-slate-700">تابعنا على وسائل التواصل الاجتماعي</h4>
                        <div class="flex items-center gap-3 mt-2">
                            <!-- WhatsApp -->
                            <a href="#" class="w-9 h-9 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.458L0 24zm6.002-3.693c1.615.957 3.178 1.462 4.736 1.463 5.485.002 9.948-4.463 9.95-9.953.001-2.66-1.025-5.16-2.887-7.026C16.001 2.923 13.506 1.897 10.85 1.897c-5.486 0-9.949 4.464-9.953 9.954-.001 2.052.541 4.06 1.567 5.814l-1.026 3.75 3.829-1.004z"></path></svg></a>
                            <!-- Facebook -->
                            <a href="#" class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h3v-9h3.6L15 8h-3V6.5C12 5.67 12.5 5 13.5 5H15V2h-2.5C9.5 2 9 3.5 9 5.5V8z"></path></svg></a>
                            <!-- Instagram -->
                            <a href="#" class="w-9 h-9 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"></path></svg></a>
                            <!-- YouTube -->
                            <a href="#" class="w-9 h-9 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.107C19.53 3.5 12 3.5 12 3.5s-7.53 0-9.388.556a3.003 3.003 0 00-2.11 2.107C0 8.018 0 12 0 12s0 3.982.502 5.837a3.003 3.003 0 002.11 2.108c1.858.555 9.388.555 9.388.555s7.53 0 9.388-.555a3.003 3.003 0 002.11-2.108C24 15.982 24 12 24 12s0-3.982-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></path></svg></a>
                        </div>
                    </div>
                </div>

                <!-- Middle footer: links -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-12">
                    <!-- Column 1 -->
                    <div class="flex flex-col gap-4">
                        <h5 class="text-sm font-bold text-slate-800">خدمة العملاء</h5>
                        <ul class="text-xs space-y-2.5 text-slate-500 font-semibold">
                            <li>تواصل معنا: 01012345678</li>
                            <li>البريد: info@2morro.com</li>
                            <li><a href="#" class="hover:text-brand-blue">سياسة الشحن والتوصيل</a></li>
                            <li><a href="#" class="hover:text-brand-blue">سياسة الاسترجاع والاستبدال</a></li>
                        </ul>
                    </div>
                    <!-- Column 2 -->
                    <div class="flex flex-col gap-4">
                        <h5 class="text-sm font-bold text-slate-800">تسوق</h5>
                        <ul class="text-xs space-y-2.5 text-slate-500 font-semibold">
                            <li><a href="{{ route('search', ['category' => 'educational-tools']) }}" class="hover:text-brand-blue">الأدوات التعليمية</a></li>
                            <li><a href="{{ route('search', ['category' => 'digital-worksheets']) }}" class="hover:text-brand-blue">الشيتات الرقمية</a></li>
                            <li><a href="{{ route('search', ['category' => 'educational-bundles']) }}" class="hover:text-brand-blue">الباقات التعليمية</a></li>
                            <li><a href="{{ route('search', ['category' => 'courses']) }}" class="hover:text-brand-blue">الكورسات والبرامج</a></li>
                        </ul>
                    </div>
                    <!-- Column 3 -->
                    <div class="flex flex-col gap-4">
                        <h5 class="text-sm font-bold text-slate-800">معلومات</h5>
                        <ul class="text-xs space-y-2.5 text-slate-500 font-semibold">
                            <li><a href="#" class="hover:text-brand-blue">من نحن</a></li>
                            <li><a href="#" class="hover:text-brand-blue">خبرة أخصائيينا</a></li>
                            <li><a href="#" class="hover:text-brand-blue">الأسئلة الشائعة</a></li>
                            <li><a href="#" class="hover:text-brand-blue">اتصل بنا</a></li>
                        </ul>
                    </div>
                    <!-- Column 4 -->
                    <div class="flex flex-col gap-4">
                        <h5 class="text-sm font-bold text-slate-800">حسابي</h5>
                        <ul class="text-xs space-y-2.5 text-slate-500 font-semibold">
                            <li><a href="{{ route('login') }}" class="hover:text-brand-blue">تسجيل الدخول</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand-blue">إنشاء حساب جديد</a></li>
                            <li><a href="#" class="hover:text-brand-blue">طلباتي</a></li>
                            <li><a href="#" class="hover:text-brand-blue">تحميلاتي الرقمية</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom footer: Copyright and Payments -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-8 border-t border-slate-200 text-xs text-slate-400 font-semibold">
                    <p>© {{ date('Y') }} تمورو (2morro). جميع الحقوق محفوظة. تم التطوير بواسطة Ox Tech.</p>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-slate-400">طرق الدفع المدعومة:</span>
                        <div class="flex items-center gap-1.5 font-extrabold text-[10px]">
                            <span class="px-2 py-0.5 border border-slate-300 rounded text-[#1A1F71] bg-white">InstaPay</span>
                            <span class="px-2 py-0.5 border border-slate-300 rounded text-red-600 bg-white">Vodafone Cash</span>
                            <span class="px-2 py-0.5 border border-slate-300 rounded text-blue-800 bg-white">COD</span>
                        </div>
                    </div>
                </div>

            </div>
        </footer>

        @yield('scripts')
    </body>
</html>
