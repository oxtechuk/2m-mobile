@extends('layouts.storefront')

@section('title', 'تمورو | أدوات تعليمية تنمي مهارات طفلك')

@section('content')
<div class="bg-white">
    <!-- 1. Hero Section -->
    <div class="max-w-7xl mx-auto px-4 pt-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-8">
            <!-- Hero Left Card (Navy brand box) -->
            <div class="lg:col-span-6 bg-slate-50 p-8 md:p-12 rounded-[32px] border border-slate-100 relative overflow-hidden flex flex-col justify-center h-full">
                <!-- Background decorative elements -->
                <div class="absolute -top-12 -left-12 w-24 h-24 rounded-full bg-brand-coral/10"></div>
                <div class="absolute -bottom-12 -right-12 w-32 h-32 rounded-full bg-brand-blue/5"></div>
                
                <h1 class="text-4xl md:text-5xl font-black text-[#102A63] leading-tight">
                    أدوات تعليمية<br>
                    <span class="text-brand-blue">تنمي مهارات طفلك</span>
                </h1>
                <p class="text-slate-500 font-semibold mt-4 text-base">
                    تعلم .. استمتع .. وتطور كل يوم مع وسائل وأدوات تعليمية تم اختيارها بعناية فائقة وتوصية من أخصائيينا لتناسب احتياج طفلك.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <a href="{{ route('search') }}" class="bg-[#102A63] hover:bg-slate-800 text-white font-bold text-sm py-3 px-8 rounded-full flex items-center justify-center gap-2 transition-all shadow-md">
                        <svg class="w-5 h-5 text-brand-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        تسوق الآن
                    </a>
                    <a href="{{ route('search') }}" class="bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm py-3 px-8 rounded-full flex items-center justify-center gap-1.5 transition-all border border-slate-200">
                        اختر حسب احتياج طفلك
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Hero Center Image -->
            <div class="lg:col-span-4 relative flex justify-center">
                <div class="w-full max-w-sm rounded-[32px] overflow-hidden bg-brand-blue/5 border border-slate-100 shadow-sm relative">
                    <img src="{{ asset('images/hero-child.png') }}" alt="طفل يلعب بألعاب تعليمية" class="w-full h-auto object-cover">
                    <!-- Subtle floaters -->
                    <div class="absolute top-4 right-4 bg-white/95 backdrop-blur py-1 px-3.5 rounded-full shadow-sm border border-slate-100 flex items-center gap-1 text-[11px] font-bold text-brand-navy">
                        <span class="w-2 h-2 rounded-full bg-brand-turquoise"></span>ألعاب مهارية مميزة
                    </div>
                </div>
            </div>

            <!-- Hero Right Trust factors -->
            <div class="lg:col-span-2 flex flex-col gap-4">
                <!-- Trust 1 -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="p-2 bg-green-50 rounded-xl text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800">منتجات آمنة</span>
                        <span class="text-[10px] text-slate-400 font-semibold mt-0.5">معتمدة ومناسبة للأطفال</span>
                    </div>
                </div>
                <!-- Trust 2 -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="p-2 bg-blue-50 rounded-xl text-brand-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800">شحن سريع</span>
                        <span class="text-[10px] text-slate-400 font-semibold mt-0.5">لكل أنحاء جمهورية مصر</span>
                    </div>
                </div>
                <!-- Trust 3 -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="p-2 bg-pink-50 rounded-xl text-brand-coral">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800">دعم عملاء مميز</span>
                        <span class="text-[10px] text-slate-400 font-semibold mt-0.5">مستعد لخدمتكم دائماً</span>
                    </div>
                </div>
                <!-- Trust 4 -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 hover:shadow-sm transition-all flex items-start gap-3">
                    <div class="p-2 bg-teal-50 rounded-xl text-brand-turquoise">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800">طرق دفع متنوعة</span>
                        <span class="text-[10px] text-slate-400 font-semibold mt-0.5">انستاباي، محفظة، كاش</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Needs and Skills Categories Section -->
    <div class="bg-slate-50 py-12 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-brand-coral rounded-full"></span>
                    تسوق حسب احتياج طفلك والمهارة
                </h2>
                <a href="{{ route('search') }}" class="text-xs font-extrabold text-brand-blue hover:underline">عرض الكل</a>
            </div>

            <!-- Categories Grid (Mocking user design styling) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                <!-- 1. أدوات تعليمية (Red) -->
                <a href="{{ route('search', ['category' => 'educational-tools']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">الأدوات التعليمية</span>
                </a>

                <!-- 2. تنمية مهارات ما قبل المدرسة (Orange) -->
                <a href="{{ route('search') }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">مهارات الروضة</span>
                </a>

                <!-- 3. تنمية اللغة (Green) -->
                <a href="{{ route('search', ['skill' => 'language-development']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">تنمية اللغة</span>
                </a>

                <!-- 4. صعوبات التعلم (Blue) -->
                <a href="{{ route('search', ['skill' => 'learning-difficulties']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">صعوبات التعلم</span>
                </a>

                <!-- 5. فرط الحركة (Orange) -->
                <a href="{{ route('search', ['need' => 'adhd']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">فرط الحركة</span>
                </a>

                <!-- 6. ضعف الانتباه (Purple) -->
                <a href="{{ route('search', ['skill' => 'attention-focus']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">ضعف الانتباه</span>
                </a>

                <!-- 7. التوحد (Turquoise) -->
                <a href="{{ route('search', ['need' => 'autism']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">التوحد</span>
                </a>

                <!-- 8. تأخر الكلام (Pink) -->
                <a href="{{ route('search', ['need' => 'speech-delay']) }}" class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition-all flex flex-col items-center text-center group">
                    <div class="w-12 h-12 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3">تأخر الكلام</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. New Arrivals (الجديد لدينا) -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-6 bg-brand-blue rounded-full"></span>
                الجديد لدينا
            </h2>
            <a href="{{ route('search') }}" class="text-xs font-extrabold text-brand-blue hover:underline">عرض الكل</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
                <!-- Product Card Component -->
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

    <!-- 4. Best Sellers (الأكثر مبيعاً) -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-6 bg-brand-turquoise rounded-full"></span>
                الأكثر مبيعاً
            </h2>
            <a href="{{ route('search') }}" class="text-xs font-extrabold text-brand-blue hover:underline">عرض الكل</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($bestSellers as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

    <!-- 5. Promotional Banners -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Banner 1: Bundles (Pink/Red) -->
            <div class="bg-red-50 p-8 rounded-3xl border border-red-100 flex flex-col justify-between group relative overflow-hidden h-72">
                <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-red-100 group-hover:scale-110 transition-transform"></div>
                <div class="z-10">
                    <span class="bg-brand-coral text-white text-[10px] font-bold py-1 px-3 rounded-full">باقات توفير</span>
                    <h3 class="text-xl font-black text-slate-800 mt-4 leading-tight">أدوات + شيتات + كورس<br>بسعر أقل</h3>
                </div>
                <a href="{{ route('search', ['category' => 'educational-bundles']) }}" class="mt-6 font-bold text-xs text-brand-navy hover:text-slate-600 transition-colors flex items-center gap-1 z-10">
                    تسوق الباقات
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>

            <!-- Banner 2: Digital Worksheets (Blue) -->
            <div class="bg-blue-50 p-8 rounded-3xl border border-blue-100 flex flex-col justify-between group relative overflow-hidden h-72">
                <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-blue-100 group-hover:scale-110 transition-transform"></div>
                <div class="z-10">
                    <span class="bg-brand-blue text-white text-[10px] font-bold py-1 px-3 rounded-full">شيتات جاهزة للطباعة</span>
                    <h3 class="text-xl font-black text-slate-800 mt-4 leading-tight">تحميل فوري لشيتات<br>تمورو التعليمية</h3>
                </div>
                <a href="{{ route('search', ['category' => 'digital-worksheets']) }}" class="mt-6 font-bold text-xs text-brand-navy hover:text-slate-600 transition-colors flex items-center gap-1 z-10">
                    تصفح الشيتات
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>

            <!-- Banner 3: Courses (Teal) -->
            <div class="bg-teal-50 p-8 rounded-3xl border border-teal-100 flex flex-col justify-between group relative overflow-hidden h-72">
                <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-teal-100 group-hover:scale-110 transition-transform"></div>
                <div class="z-10">
                    <span class="bg-brand-turquoise text-white text-[10px] font-bold py-1 px-3 rounded-full">كورسات وبرامج</span>
                    <h3 class="text-xl font-black text-slate-800 mt-4 leading-tight">تعلم بخطوات عملية<br>مع نخبة من المتخصصين</h3>
                </div>
                <a href="{{ route('search', ['category' => 'courses']) }}" class="mt-6 font-bold text-xs text-brand-navy hover:text-slate-600 transition-colors flex items-center gap-1 z-10">
                    استكشف البرامج
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 6. Shop By Age (تسوق حسب العمر) -->
    <div class="bg-slate-50 py-16 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-xl font-bold text-slate-800 text-center mb-12 flex items-center justify-center gap-2">
                <span class="w-2.5 h-6 bg-brand-blue rounded-full"></span>
                تسوق حسب سن طفلك
            </h2>

            <div class="flex flex-wrap justify-center items-center gap-8">
                <!-- Under 2 years -->
                <a href="{{ route('search', ['age' => '2-3']) }}" class="flex flex-col items-center group">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-200 group-hover:border-brand-blue group-hover:scale-105 transition-all shadow-sm flex items-center justify-center font-black text-slate-600 text-lg">
                        👶
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3 group-hover:text-brand-blue">أقل من سنتين</span>
                </a>
                
                <!-- 2-3 years -->
                <a href="{{ route('search', ['age' => '2-3']) }}" class="flex flex-col items-center group">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-200 group-hover:border-brand-blue group-hover:scale-105 transition-all shadow-sm flex items-center justify-center font-black text-slate-600 text-lg">
                        🧸
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3 group-hover:text-brand-blue">2 - 3 سنوات</span>
                </a>

                <!-- 4-5 years -->
                <a href="{{ route('search', ['age' => '4-5']) }}" class="flex flex-col items-center group">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-200 group-hover:border-brand-blue group-hover:scale-105 transition-all shadow-sm flex items-center justify-center font-black text-slate-600 text-lg">
                        🎨
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3 group-hover:text-brand-blue">4 - 5 سنوات</span>
                </a>

                <!-- 6-8 years -->
                <a href="{{ route('search', ['age' => '6-8']) }}" class="flex flex-col items-center group">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-200 group-hover:border-brand-blue group-hover:scale-105 transition-all shadow-sm flex items-center justify-center font-black text-slate-600 text-lg">
                        📚
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3 group-hover:text-brand-blue">6 - 8 سنوات</span>
                </a>

                <!-- 9-12 years -->
                <a href="{{ route('search', ['age' => '9-12']) }}" class="flex flex-col items-center group">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-slate-200 group-hover:border-brand-blue group-hover:scale-105 transition-all shadow-sm flex items-center justify-center font-black text-slate-600 text-lg">
                        🔬
                    </div>
                    <span class="text-xs font-bold text-slate-700 mt-3 group-hover:text-brand-blue">9 - 12 سنة</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 7. Why 2morro? (لماذا تختار تمورو؟) -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <h2 class="text-xl font-bold text-slate-800 text-center mb-12 flex items-center justify-center gap-2">
            لماذا تختار 2morro؟
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
            <!-- 1 -->
            <div class="flex flex-col items-center p-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-brand-blue flex items-center justify-center mb-4">
                    📦
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">إرجاع سهل</h4>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">خلال 14 يوماً من استلام الطلب</p>
            </div>
            <!-- 2 -->
            <div class="flex flex-col items-center p-4">
                <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center mb-4">
                    ⚡
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">شحن سريع</h4>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">توصيل لكافة المحافظات المصرية</p>
            </div>
            <!-- 3 -->
            <div class="flex flex-col items-center p-4">
                <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mb-4">
                    💰
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">أسعار تنافسية</h4>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">أفضل قيمة مقابل جودة وفائدة</p>
            </div>
            <!-- 4 -->
            <div class="flex flex-col items-center p-4">
                <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center mb-4">
                    ⭐
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">جودة عالية</h4>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">مختارة بعناية ودقة تامة</p>
            </div>
            <!-- 5 -->
            <div class="flex flex-col items-center p-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-brand-coral flex items-center justify-center mb-4">
                    🛡️
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">منتجات آمنة 100%</h4>
                <p class="text-[10px] text-slate-400 font-semibold mt-1">خامات صديقة للطفل ومجربة</p>
            </div>
        </div>
    </div>

    <!-- 8. Testimonials (ماذا يقول عملؤنا) -->
    <div class="bg-slate-50 py-16 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-xl font-bold text-slate-800 text-center mb-12">ماذا يقول عملاؤنا؟</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs text-slate-500 font-semibold italic leading-relaxed">
                        "جودة المنتجات ممتازة جداً ونفعتني في تدريب ابني في البيت، التوصيل كان سريع والخدمة ممتازة، شكرًا لكم!"
                    </p>
                    <div class="flex items-center gap-3 mt-6">
                        <div class="w-10 h-10 rounded-full bg-slate-200 font-extrabold text-xs flex items-center justify-center">أ.م</div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-800">أمل محمد</span>
                            <span class="text-[10px] text-slate-400 font-semibold">أم لطفل 5 سنوات</span>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs text-slate-500 font-semibold italic leading-relaxed">
                        "الشيتات الرقمية اختصرت عليا وقت كتير جداً، كأخصائي بستخدمها يومياً مع الحالات وبتجيب نتايج ممتازة معاهم."
                    </p>
                    <div class="flex items-center gap-3 mt-6">
                        <div class="w-10 h-10 rounded-full bg-slate-200 font-extrabold text-xs flex items-center justify-center">أ.س</div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-800">أحمد سليمان</span>
                            <span class="text-[10px] text-slate-400 font-semibold">أخصائي تخاطب وتعديل سلوك</span>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <p class="text-xs text-slate-500 font-semibold italic leading-relaxed">
                        "اشتريت باقة التخاطب المنزلية وكانت متكاملة جداً، الكروت مع دليل الاستخدام والفيديو فادوني كتير جداً مع بنتي."
                    </p>
                    <div class="flex items-center gap-3 mt-6">
                        <div class="w-10 h-10 rounded-full bg-slate-200 font-extrabold text-xs flex items-center justify-center">س.خ</div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-800">سارة خالد</span>
                            <span class="text-[10px] text-slate-400 font-semibold">أم لطفلة 3 سنوات</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
