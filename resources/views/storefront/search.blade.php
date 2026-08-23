@extends('layouts.storefront')

@section('title', 'تصفح منتجات تمورو | أدوات وشيتات تعليمية')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Page Title & Results Counter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-[#102A63]">
                    تصفح منتجات وأنشطة تمورو
                </h1>
                <p class="text-xs text-slate-400 font-semibold mt-1">
                    @if(request('q'))
                        نتائج البحث عن: <span class="text-brand-blue">"{{ request('q') }}"</span> • 
                    @endif
                    تم العثور على {{ $products->total() }} منتج
                </p>
            </div>

            <!-- Sorting & Layout -->
            <form action="{{ route('search') }}" method="GET" id="sortForm" class="flex items-center gap-2 text-xs">
                <!-- Keep existing query inputs hidden -->
                @foreach(request()->except(['sort', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                
                <span class="font-bold text-slate-500 whitespace-nowrap">ترتيب حسب:</span>
                <select name="sort" onchange="document.getElementById('sortForm').submit()" class="border-slate-300 rounded-lg text-xs font-semibold py-1.5 px-4 text-slate-600 focus:border-brand-blue focus:ring-0">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Filters Sidebar (Right Column in RTL, span 3) -->
            <aside class="lg:col-span-3 bg-slate-50 p-6 rounded-3xl border border-slate-200 h-fit">
                <div class="flex justify-between items-center pb-4 border-b border-slate-200 mb-6">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        تصفية النتائج
                    </h3>
                    @if(request()->anyFilled(['q', 'category', 'age', 'need', 'skill', 'type']))
                        <a href="{{ route('search') }}" class="text-[10px] font-extrabold text-red-500 hover:underline">مسح الكل</a>
                    @endif
                </div>

                <!-- 1. Category Filter -->
                <div class="mb-6 pb-6 border-b border-slate-200">
                    <h4 class="text-xs font-bold text-slate-700 mb-3">القسم الرئيسي</h4>
                    <div class="flex flex-col gap-2 text-xs">
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="flex items-center justify-between font-semibold {{ !request('category') ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span>كل الأقسام</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug]) }}" class="flex items-center justify-between font-semibold {{ request('category') === $cat->slug ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[10px] bg-slate-200/50 py-0.5 px-2 rounded-full font-bold text-slate-400">{{ $cat->products()->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Age Groups Filter -->
                <div class="mb-6 pb-6 border-b border-slate-200">
                    <h4 class="text-xs font-bold text-slate-700 mb-3">حسب عمر الطفل</h4>
                    <div class="flex flex-col gap-2.5 text-xs font-semibold">
                        <a href="{{ request()->fullUrlWithQuery(['age' => null]) }}" class="flex items-center gap-2 {{ !request('age') ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ !request('age') ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>الكل</span>
                        </a>
                        @foreach($ageGroups as $age)
                            <a href="{{ request()->fullUrlWithQuery(['age' => $age->slug]) }}" class="flex items-center gap-2 {{ request('age') === $age->slug ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request('age') === $age->slug ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                                <span>{{ $age->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Needs Filter -->
                <div class="mb-6 pb-6 border-b border-slate-200">
                    <h4 class="text-xs font-bold text-slate-700 mb-3">حسب احتياج طفلك</h4>
                    <div class="flex flex-col gap-2.5 text-xs font-semibold">
                        <a href="{{ request()->fullUrlWithQuery(['need' => null]) }}" class="flex items-center gap-2 {{ !request('need') ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ !request('need') ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>الكل</span>
                        </a>
                        @foreach($needs as $need)
                            <a href="{{ request()->fullUrlWithQuery(['need' => $need->slug]) }}" class="flex items-center gap-2 {{ request('need') === $need->slug ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request('need') === $need->slug ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                                <span>{{ $need->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Skills Filter -->
                <div class="mb-6 pb-6 border-b border-slate-200">
                    <h4 class="text-xs font-bold text-slate-700 mb-3">حسب المهارة المستهدفة</h4>
                    <div class="flex flex-col gap-2.5 text-xs font-semibold">
                        <a href="{{ request()->fullUrlWithQuery(['skill' => null]) }}" class="flex items-center gap-2 {{ !request('skill') ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ !request('skill') ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>الكل</span>
                        </a>
                        @foreach($skills as $skill)
                            <a href="{{ request()->fullUrlWithQuery(['skill' => $skill->slug]) }}" class="flex items-center gap-2 {{ request('skill') === $skill->slug ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request('skill') === $skill->slug ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                                <span>{{ $skill->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 5. Product Type Filter -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 mb-3">طبيعة المنتج</h4>
                    <div class="flex flex-col gap-2.5 text-xs font-semibold">
                        <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="flex items-center gap-2 {{ !request('type') ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ !request('type') ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>الكل</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'physical']) }}" class="flex items-center gap-2 {{ request('type') === 'physical' ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'physical' ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>أدوات مادية وملموسة</span>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'digital']) }}" class="flex items-center gap-2 {{ request('type') === 'digital' ? 'text-brand-blue font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request('type') === 'digital' ? 'bg-brand-blue' : 'bg-slate-300' }}"></span>
                            <span>شيتات رقمية قابلة للطباعة</span>
                        </a>
                    </div>
                </div>

            </aside>

            <!-- Catalog Grid (Left Column, span 9) -->
            <div class="lg:col-span-9 flex flex-col justify-between">
                
                @if($products->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-slate-50 rounded-3xl border border-slate-200 py-16 px-6 text-center flex flex-col items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                            🔍
                        </div>
                        <h3 class="text-base font-bold text-slate-700">لم نعثر على أي منتجات مطابقة للتصفية</h3>
                        <p class="text-xs text-slate-400 max-w-sm">جرب إزالة بعض الفلاتر المحددة أو ابحث بكلمات مختلفة مثل: "لغة"، "تأسيس"، "تخاطب".</p>
                        <a href="{{ route('search') }}" class="bg-[#102A63] hover:bg-slate-800 text-white font-bold text-xs py-2 px-6 rounded-full mt-4 transition-all">تصفح كل المنتجات</a>
                    </div>
                @else
                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            @include('storefront.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
@endsection
