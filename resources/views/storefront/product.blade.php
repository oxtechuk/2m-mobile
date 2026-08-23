@extends('layouts.storefront')

@section('title', $product->name . ' | تمورو')

@section('content')
<div class="bg-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-brand-blue">الرئيسية</a>
            <span>/</span>
            @if($product->categories->isNotEmpty())
                <a href="{{ route('search', ['category' => $product->categories->first()->slug]) }}" class="hover:text-brand-blue">
                    {{ $product->categories->first()->name }}
                </a>
                <span>/</span>
            @endif
            <span class="text-slate-600 select-none">{{ $product->name }}</span>
        </div>

        <!-- Product Gallery and Checkout Box -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Gallery (Left Column) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 aspect-square flex items-center justify-center overflow-hidden">
                    @if($product->images && count($product->images) > 0)
                        <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                </div>

                @if($product->images && count($product->images) > 1)
                    <!-- Small thumbs -->
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($product->images as $img)
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-2 cursor-pointer aspect-square flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-contain">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Basic Checkout Info (Right Column) -->
            <div class="lg:col-span-7 flex flex-col justify-between">
                <div>
                    <!-- Badge & Tags -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if($product->badge)
                            <span class="bg-brand-coral text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-xs">
                                {{ $product->badge }}
                            </span>
                        @endif
                        <span class="bg-slate-100 text-slate-600 text-[10px] font-extrabold px-3 py-1 rounded-full">
                            {{ $product->type === 'digital' ? 'تحميل فوري' : 'منتج مادي' }}
                        </span>
                        @if($product->ageGroups->isNotEmpty())
                            <span class="bg-blue-50 text-brand-blue text-[10px] font-extrabold px-3 py-1 rounded-full">
                                سن {{ $product->ageGroups->first()->name }}
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl md:text-3xl font-black text-[#102A63] mt-4 leading-snug">
                        {{ $product->name }}
                    </h1>

                    <!-- Ratings -->
                    <div class="flex items-center gap-2 mt-3 text-slate-400">
                        <div class="flex items-center text-yellow-400 text-sm">
                            ★ ★ ★ ★ ★
                        </div>
                        <span class="text-xs font-bold mt-0.5">(12 تقييم عملاء موثق)</span>
                    </div>

                    <!-- Short Description -->
                    <p class="text-slate-500 font-semibold mt-6 text-sm leading-relaxed">
                        {{ $product->short_description ?: $product->description }}
                    </p>

                    <!-- Fast specifications box -->
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-slate-400 font-bold">العمر المناسب</span>
                            <span class="text-xs font-extrabold text-slate-700">
                                {{ $product->ageGroups->first()->name ?? 'سنتين فما فوق' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 border-r border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold">المهارة المستهدفة</span>
                            <span class="text-xs font-extrabold text-slate-700">
                                {{ $product->skills->first()->name ?? 'تنمية المهارات' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 border-r border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold">نوع المنتج</span>
                            <span class="text-xs font-extrabold text-slate-700">
                                {{ $product->type === 'digital' ? 'ملف PDF للطباعة' : 'علبة/لعبة مادية' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 border-r border-slate-200">
                            <span class="text-[10px] text-slate-400 font-bold">حالة التوفر</span>
                            <span class="text-xs font-extrabold text-green-600">
                                {{ $product->stock > 0 || $product->type === 'digital' ? 'متوفر حالياً' : 'نفذت الكمية' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100">
                    <!-- Pricing -->
                    <div class="flex items-baseline gap-3 mb-6">
                        @if($product->sale_price)
                            <span class="text-3xl font-black text-brand-coral">{{ number_format($product->sale_price, 2) }} ج.م</span>
                            <span class="text-base text-slate-400 line-through font-bold">{{ number_format($product->price, 2) }} ج.م</span>
                            <span class="text-xs text-green-600 font-black bg-green-50 py-1 px-3 rounded-full border border-green-100">
                                توفير {{ number_format($product->price - $product->sale_price, 2) }} ج.م
                            </span>
                        @else
                            <span class="text-3xl font-black text-[#102A63]">{{ number_format($product->price, 2) }} ج.م</span>
                        @endif
                    </div>

                    <!-- Cart and Buy buttons -->
                    <div class="flex flex-col sm:flex-row gap-4" x-data="{ qty: 1 }">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow flex gap-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <!-- Quantity select -->
                            @if($product->type !== 'digital' && $product->type !== 'course')
                                <div class="flex items-center border border-slate-300 rounded-2xl overflow-hidden bg-white">
                                    <button type="button" @click="if(qty > 1) qty--" class="px-4 py-3 text-slate-500 font-extrabold hover:bg-slate-50 transition-colors">-</button>
                                    <input type="number" name="quantity" x-model.number="qty" min="1" max="{{ $product->stock }}" class="w-12 border-0 text-center font-bold text-slate-700 focus:ring-0 p-0">
                                    <button type="button" @click="if(qty < {{ $product->stock }}) qty++" class="px-4 py-3 text-slate-500 font-extrabold hover:bg-slate-50 transition-colors">+</button>
                                </div>
                            @else
                                <input type="hidden" name="quantity" value="1">
                            @endif

                            <button type="submit" class="flex-grow bg-[#102A63] hover:bg-slate-800 text-white font-bold text-sm py-3.5 px-8 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md">
                                <svg class="w-5 h-5 text-brand-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                أضف إلى السلة
                            </button>
                        </form>

                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow sm:flex-grow-0">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" :value="qty">
                            <input type="hidden" name="buy_now" value="1">
                            <button type="submit" class="w-full bg-brand-coral hover:bg-red-500 text-white font-bold text-sm py-3.5 px-10 rounded-2xl flex items-center justify-center gap-1.5 transition-all shadow-md">
                                شراء الآن
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Detailed Specs: Description, Benefits, How to Use -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mt-16 pt-16 border-t border-slate-100">
            <!-- Left Info Panel (Details) -->
            <div class="lg:col-span-8 flex flex-col gap-10">
                <!-- Description -->
                <div class="flex flex-col gap-3">
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-5 bg-brand-blue rounded-full"></span>
                        الوصف الكامل للمنتج
                    </h3>
                    <p class="text-slate-500 font-semibold text-sm leading-relaxed whitespace-pre-line">
                        {{ $product->description }}
                    </p>
                </div>

                <!-- Benefits (الفوائد والمهارات المكتسبة) -->
                @if($product->benefits && count($product->benefits) > 0)
                    <div class="flex flex-col gap-3 bg-green-50/50 p-6 rounded-3xl border border-green-100/50">
                        <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-5 bg-green-500 rounded-full"></span>
                            الفوائد والمهارات التي يطورها المنتج لطفلك
                        </h3>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 text-slate-600 text-xs font-bold">
                            @foreach($product->benefits as $benefit)
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                                    {{ $benefit }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- How to Use (طريقة اللعب والاستخدام) -->
                @if($product->how_to_use && count($product->how_to_use) > 0)
                    <div class="flex flex-col gap-3">
                        <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-5 bg-brand-coral rounded-full"></span>
                            طريقة الاستخدام والتطبيق مع طفلك
                        </h3>
                        <ol class="flex flex-col gap-4 mt-2 text-slate-500 text-sm font-semibold">
                            @foreach($product->how_to_use as $index => $step)
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-brand-coral/10 text-brand-coral flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                        {{ $index + 1 }}
                                    </span>
                                    <span>{{ $step }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <!-- Video embed -->
                @if($product->video_url)
                    <div class="flex flex-col gap-3">
                        <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-5 bg-brand-turquoise rounded-full"></span>
                            فيديو توضيحي لطريقة الاستخدام
                        </h3>
                        <div class="aspect-video w-full rounded-3xl overflow-hidden shadow-sm border border-slate-100">
                            <iframe class="w-full h-full" src="{{ $product->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Details Panel (Box contents & support info) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- What's included -->
                @if($product->whats_included)
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200">
                        <h4 class="text-xs font-extrabold text-slate-800 mb-3 flex items-center gap-1.5">
                            📦 محتويات العبوة بالتفصيل:
                        </h4>
                        <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                            {{ $product->whats_included }}
                        </p>
                    </div>
                @endif

                <!-- Suitable for -->
                @if($product->suitable_for)
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200">
                        <h4 class="text-xs font-extrabold text-slate-800 mb-3 flex items-center gap-1.5">
                            🎯 الفئة المستهدفة والحالات:
                        </h4>
                        <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                            {{ $product->suitable_for }}
                        </p>
                    </div>
                @endif

                <!-- Payment Details Panel -->
                <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100">
                    <h4 class="text-xs font-extrabold text-[#102A63] mb-3 flex items-center gap-1.5">
                        💳 طريقة الدفع والتأكيد:
                    </h4>
                    <p class="text-[11px] text-slate-600 font-semibold leading-relaxed">
                        يتم الدفع محلياً من خلال تحويل <b>انستاباي (InstaPay)</b> أو عبر <b>المحافظ الإلكترونية (فودافون كاش)</b>. بعد إتمام الطلب، سيظهر لك رقم الحساب وباب لرفع لقطة الشاشة (إثبات التحويل) لتأكيد الطلب وشحنه فوراً.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. Related Products -->
        @if($relatedProducts->isNotEmpty())
            <div class="mt-16 pt-16 border-t border-slate-100">
                <h3 class="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-brand-blue rounded-full"></span>
                    منتجات ننصح بها طفلك
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        @include('storefront.partials.product-card', ['product' => $related])
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
