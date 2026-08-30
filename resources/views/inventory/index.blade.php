<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Inventory Listing (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-boxes-stacked ml-2 text-[#D41414]"></i>جرد المخزون والكميات</h2>
                    <p class="text-xs text-gray-500 mt-1">حصر كميات المنتجات والقطع المتوفرة في الفرع الحالي.</p>
                </div>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('inventory.index') }}" class="relative shrink-0">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="البحث باسم المنتج أو الـ SKU..." 
                    value="{{ request('search') }}"
                    class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
            </form>

            <!-- Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">المنتج</th>
                            <th class="pb-2">القسم</th>
                            <th class="pb-2">الرصيد المتاح</th>
                            <th class="pb-2">حد الأمان</th>
                            <th class="pb-2">سيريال/IMEI</th>
                            <th class="pb-2">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($stock as $inv)
                            @php
                                $prod = $inv->product;
                                if (!$prod) continue;
                                $status = 'ok';
                                $minStock = $prod->minimum_stock ?? 5;
                                if ($inv->quantity <= 0) {
                                    $status = 'empty';
                                } elseif ($inv->quantity <= $minStock) {
                                    $status = 'warning';
                                }
                            @endphp
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3">
                                    <span class="font-semibold text-white block">{{ $prod->name }}</span>
                                    <span class="text-[10px] text-gray-500 font-mono">SKU: {{ $prod->sku }}</span>
                                </td>
                                <td class="py-3 text-gray-400">{{ $prod->category->name ?? 'عام' }}</td>
                                <td class="py-3 font-bold font-mono text-white text-sm">{{ $inv->quantity }} {{ $prod->unit }}</td>
                                <td class="py-3 font-mono text-gray-500">{{ $prod->minimum_stock }}</td>
                                <td class="py-3">
                                    @if($prod->has_serials)
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-blue-500/10 border border-blue-500/20 text-blue-400">نعم</span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($status === 'empty')
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center w-fit">
                                            <span class="w-1 h-1 rounded-full bg-rose-500 ml-1"></span> نفذ تماماً
                                        </span>
                                    @elseif($status === 'warning')
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center w-fit">
                                            <span class="w-1 h-1 rounded-full bg-amber-500 ml-1"></span> رصيد حرج
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center w-fit">
                                            <span class="w-1 h-1 rounded-full bg-emerald-500 ml-1"></span> متوفر
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">لا يوجد بضائع في مخزن الفرع حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Adjustments & Transfers forms (1 Col) -->
        <div class="space-y-6">
            
            <!-- Quick Adjustment Form -->
            <div class="glass-panel p-4 md:p-6 space-y-4">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">
                    <i class="fa-solid fa-sliders ml-1.5 text-[#D41414]"></i>تسوية رصيد المخزن (تعديل كميات)
                </h3>
                
                <form method="POST" action="{{ route('inventory.restock') }}" class="space-y-3">
                    @csrf
                    
                    <div class="space-y-1">
                        <label for="product_id" class="block text-[10px] text-gray-400">المنتج المراد تسويته</label>
                        <select name="product_id" required class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                            <option value="">اختر المنتج...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="quantity" class="block text-[10px] text-gray-400">فارق الكمية (+ إضافة / - خصم)</label>
                        <input type="number" name="quantity" required placeholder="مثال: 10 أو -2" class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]">
                    </div>

                    <div class="space-y-1">
                        <label for="notes" class="block text-[10px] text-gray-400">سبب التسوية</label>
                        <input type="text" name="notes" placeholder="مثال: تصحيح جرد، تالف، فحص دوري" class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                    </div>

                    <button type="submit" class="w-full py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                        تحديث رصيد المخزن <i class="fa-solid fa-arrows-rotate mr-1"></i>
                    </button>
                </form>
            </div>

            <!-- Inter-branch Stock Transfer Form -->
            <div class="glass-panel p-4 md:p-6 space-y-4">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">
                    <i class="fa-solid fa-truck ml-1.5 text-blue-500"></i>تحويل بضائع إلى فرع آخر
                </h3>
                
                <form method="POST" action="{{ route('inventory.transfer') }}" class="space-y-3">
                    @csrf
                    
                    <div class="space-y-1">
                        <label for="product_id" class="block text-[10px] text-gray-400">المنتج المراد تحويله</label>
                        <select name="product_id" required class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                            <option value="">اختر المنتج...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="target_branch_id" class="block text-[10px] text-gray-400">الفرع المستهدف للاستقبال</label>
                        <select name="target_branch_id" required class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                            <option value="">اختر فرع الاستقبال...</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="quantity" class="block text-[10px] text-gray-400">الكمية المحولة</label>
                        <input type="number" min="1" name="quantity" required placeholder="مثال: 5" class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]">
                    </div>

                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-lg">
                        بدء نقل البضائع للأفرع <i class="fa-solid fa-paper-plane mr-1"></i>
                    </button>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>