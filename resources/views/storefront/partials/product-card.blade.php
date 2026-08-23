<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
    
    <!-- Top half: Badge & Image -->
    <div class="relative bg-slate-50 aspect-square flex items-center justify-center p-4 overflow-hidden">
        <!-- Badges -->
        @if($product->badge)
            <span class="absolute top-3 right-3 z-10 text-[9px] font-extrabold px-2.5 py-1 rounded-full text-white bg-brand-coral shadow-sm">
                {{ $product->badge }}
            </span>
        @endif

        <!-- Product Image -->
        @if($product->images && count($product->images) > 0)
            <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif

        <!-- Quick metadata banner -->
        @if($product->suitable_for)
            <div class="absolute bottom-0 inset-x-0 bg-slate-900/40 backdrop-blur-xs text-white text-[10px] font-bold py-1 px-3 translate-y-full group-hover:translate-y-0 transition-transform duration-350 flex justify-between items-center">
                <span>{{ Str::limit($product->suitable_for, 28) }}</span>
            </div>
        @endif
    </div>

    <!-- Bottom half: Info, price & Add to Cart -->
    <div class="p-4 flex flex-col justify-between flex-grow">
        <div>
            <!-- Category / Age Group metadata -->
            <div class="flex items-center gap-1.5 text-[10px] font-extrabold text-slate-400">
                @if($product->ageGroups && $product->ageGroups->count() > 0)
                    <span>سن {{ $product->ageGroups->first()->name }}</span>
                @else
                    <span>سن سنتين فما فوق</span>
                @endif
                <span>•</span>
                <span class="text-brand-turquoise">{{ $product->type === 'digital' ? 'تحميل رقمي PDF' : 'منتج مادي' }}</span>
            </div>

            <!-- Title -->
            <h3 class="text-xs font-bold text-slate-800 mt-2 leading-tight min-h-[32px] group-hover:text-brand-blue transition-colors">
                <a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            <!-- Review ratings (stars) -->
            <div class="flex items-center gap-1 mt-1 text-slate-300">
                @php
                    $rating = $product->reviews()->where('is_approved', true)->avg('rating') ?: 5;
                    $reviewCount = $product->reviews()->where('is_approved', true)->count() ?: 12;
                @endphp
                <div class="flex items-center text-yellow-400 text-xs">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($rating))
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <span class="text-[9px] text-slate-400 font-bold mt-0.5">({{ $reviewCount }})</span>
            </div>
        </div>

        <div class="mt-4">
            <!-- Pricing -->
            <div class="flex items-baseline gap-2 mb-3">
                @if($product->sale_price)
                    <span class="text-sm font-extrabold text-brand-coral">{{ number_format($product->sale_price, 2) }} ج.م</span>
                    <span class="text-[10px] text-slate-400 line-through font-semibold">{{ number_format($product->price, 2) }} ج.م</span>
                @else
                    <span class="text-sm font-extrabold text-brand-navy">{{ number_format($product->price, 2) }} ج.م</span>
                @endif
            </div>

            <!-- Add to Cart action form -->
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="w-full bg-[#102A63] hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors border border-slate-200 shadow-xs">
                    <svg class="w-4 h-4 text-brand-turquoise" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    أضف للسلة
                </button>
            </form>
        </div>
    </div>

</div>
