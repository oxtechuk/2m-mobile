<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-rotate-left text-[#D41414]"></i>
                    <span>إجراء مرتجع مبيعات جديد</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">ابحث باسم المنتج أو رقم الفاتورة أو الباركود لعرض تاريخ ووقت الشراء وإجراء المرتجع</p>
            </div>
            <a href="{{ route('returns.index') }}" class="px-3.5 py-2 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-right"></i> عودة للمرتجعات
            </a>
        </div>

        <!-- Search Form (Search by Product Name, Barcode, IMEI, Invoice Number or Customer) -->
        <div class="glass-panel p-5 rounded-2xl">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-[#D41414]"></i>
                <span>البحث عن عملية شراء (باسم المنتج، رقم الفاتورة، الباركود، الـ IMEI، أو العميل)</span>
            </h3>
            <form method="GET" action="{{ route('returns.create') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                        <i class="fa-solid fa-[#D41414] fa-mobile-screen-button text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ $search ?? request('search', request('invoice_number', '')) }}"
                        placeholder="ابحث باسم المنتج (مثال: شاحن أنكر)، الباركود، رقم الفاتورة، أو العميل..." 
                        class="w-full pr-9 pl-3 py-2.5 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                        required
                    >
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold text-xs rounded-xl transition shadow-lg glow-primary flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass"></i> بحث
                </button>
            </form>
        </div>

        <!-- Multiple Search Results List -->
        @if(isset($matchingSales) && $matchingSales->count() > 0)
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-400"></i>
                <span>نتائج البحث المتطابقة ({{ $matchingSales->count() }} فاتورة):</span>
            </h3>

            <div class="grid grid-cols-1 gap-3">
                @foreach($matchingSales as $s)
                <div class="glass-panel p-4 rounded-2xl border border-white/10 hover:border-[#D41414]/40 transition flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-white text-sm">#{{ $s->invoice_number }}</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">
                                <i class="fa-solid fa-clock ml-1"></i> تاريخ الشراء: {{ $s->created_at->format('Y-m-d h:i A') }} ({{ $s->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-xs text-gray-300">
                            <span>العميل: <strong class="text-white">{{ $s->customer->name ?? 'عميل نقدي' }}</strong></span>
                            <span>الفرع: <strong class="text-white">{{ $s->branch->name ?? 'عام' }}</strong></span>
                            <span>الإجمالي: <strong class="text-emerald-400 font-mono">{{ number_format($s->total, 2) }} {{ setting('default_currency', 'ج.م') }}</strong></span>
                        </div>

                        <!-- Product Names list snippet -->
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($s->items as $it)
                            <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-gray-300 text-[10px]">
                                {{ $it->product->name ?? 'منتج' }} ({{ $it->quantity }}x)
                            </span>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('returns.create', ['sale_id' => $s->id]) }}" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-xl text-xs transition shadow-md flex items-center justify-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-check"></i> تحديد للإرجاع
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @elseif(!empty($search) && !$sale)
        <div class="glass-panel p-8 text-center text-gray-400 rounded-2xl">
            <i class="fa-solid fa-circle-exclamation text-3xl text-amber-400/50 mb-2"></i>
            <p class="text-xs">لم يتم العثور على أي فاتورة شراء مطابقة لـ "{{ $search }}".</p>
        </div>
        @endif

        <!-- Single Selected Sale Form -->
        @if($sale)
        <form method="POST" action="{{ route('returns.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">

            <!-- Sale Summary Card with Prominent Purchase Date & Time -->
            <div class="glass-panel p-5 rounded-2xl border border-white/10 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center border-b border-white/5 pb-3 gap-3">
                    <div>
                        <span class="text-[10px] text-gray-400 block">الفاتورة المختارة:</span>
                        <h3 class="text-lg font-mono font-black text-white">#{{ $sale->invoice_number }}</h3>
                    </div>
                    
                    <!-- Purchase Date and Time Badge -->
                    <div class="px-3.5 py-2 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs font-bold flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                        <div>
                            <span class="block text-[10px] text-gray-400">تاريخ ووقت الشراء:</span>
                            <span class="font-mono text-xs text-white">{{ $sale->created_at->format('Y-m-d h:i A') }} ({{ $sale->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 text-xs text-gray-300 border-b border-white/5 pb-3">
                    <span>الفرع: <strong class="text-white">{{ $sale->branch->name ?? 'عام' }}</strong></span>
                    <span>العميل: <strong class="text-white">{{ $sale->customer->name ?? 'عميل نقدي' }}</strong></span>
                    <span>المسؤول: <strong class="text-white">{{ $sale->user->name ?? '-' }}</strong></span>
                    <span>الإجمالي: <strong class="text-emerald-400 font-mono">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</strong></span>
                </div>

                <!-- Items Return Selection Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 uppercase">
                            <tr>
                                <th class="px-4 py-3 font-semibold">الصنف / المنتج</th>
                                <th class="px-4 py-3 font-semibold text-center">الكمية المباعة</th>
                                <th class="px-4 py-3 font-semibold text-center">سعر الوحدة</th>
                                <th class="px-4 py-3 font-semibold text-center">الكمية المراد إرجاعها</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-gray-300">
                            @foreach($sale->items as $index => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="hidden" name="items[{{ $index }}][sale_item_id]" value="{{ $item->id }}">
                                    <p class="font-bold text-white text-xs">{{ $item->product->name ?? 'منتج غير معرف' }}</p>
                                    @if($item->serials && $item->serials->count() > 0)
                                        <p class="text-[10px] text-amber-400 font-mono mt-0.5">IMEI: {{ $item->serials->pluck('serial_number')->join(', ') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-white">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-emerald-400">{{ number_format($item->unit_price, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center max-w-[120px] mx-auto">
                                        <input 
                                            type="number" 
                                            name="items[{{ $index }}][qty_return]" 
                                            value="0" 
                                            min="0" 
                                            max="{{ $item->quantity }}" 
                                            step="1"
                                            class="w-20 px-2 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-center font-bold text-white focus:outline-none focus:border-[#D41414] text-xs"
                                        >
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Reason for return -->
                <div class="pt-3 border-t border-white/5 space-y-1.5">
                    <label class="text-xs font-bold text-gray-300">سبب الإرجاع (اختياري)</label>
                    <textarea 
                        name="reason" 
                        rows="2"
                        placeholder="أدخل سبب إرجاع المنتجات (تالف، تغيير رأي العميل، استبدال...)"
                        class="w-full p-3 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                    ></textarea>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('returns.index') }}" class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 font-bold rounded-xl text-xs transition">
                    إلغاء
                </a>
                <button type="submit" onclick="return confirm('هل أنت تأكد من معالجة هذا المرتجع والتعديل على المخزن والخزينة؟')" class="px-6 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-xl text-xs transition shadow-lg glow-primary flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> تأكيد وحفظ المرتجع
                </button>
            </div>
        </form>
        @endif

    </div>
</x-app-layout>
