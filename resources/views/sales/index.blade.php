<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/5 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-file-invoice-dollar ml-2 text-[#D41414]"></i>فواتير المبيعات</h2>
                <p class="text-xs text-gray-500 mt-1">مراجعة والبحث في فواتير المبيعات الصادرة وإلغاء العمليات الخاطئة.</p>
            </div>
            @can('create-sale')
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-lg transition shadow-lg flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register"></i> فاتورة بيع جديدة
            </a>
            @endcan
        </div>

        <!-- Filter Search Box -->
        <div class="glass-panel p-4">
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="البحث برقم الفاتورة أو اسم العميل أو الهاتف..." 
                        value="{{ request('search') }}"
                        class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    بحث وتصفية
                </button>
            </form>
        </div>

        <!-- Invoices List -->
        <div class="glass-panel p-4 md:p-6">
            <!-- Desktop View Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">رقم الفاتورة</th>
                            <th class="pb-2">العميل</th>
                            <th class="pb-2">الكاشير</th>
                            <th class="pb-2">طريقة الدفع</th>
                            <th class="pb-2">الفرع</th>
                            <th class="pb-2">الإجمالي النهائي</th>
                            <th class="pb-2">الحالة</th>
                            <th class="pb-2 text-left">خيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-mono font-bold text-white">{{ $sale->invoice_number }}</td>
                                <td class="py-3 text-gray-300">{{ $sale->customer->name ?? 'عميل نقدي عام' }}</td>
                                <td class="py-3">{{ $sale->user->name }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-white/5 border border-white/10">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-400">{{ $sale->branch->name ?? 'المركز الرئيسي' }}</td>
                                <td class="py-3 font-bold text-emerald-400 font-mono">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="py-3">
                                    @if($sale->status === 'completed')
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مكتملة</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">ملغاة</span>
                                    @endif
                                </td>
                                <td class="py-3 text-left">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded transition" title="تفاصيل الفاتورة">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded transition" title="طباعة الفاتورة">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">لا توجد فواتير مبيعات مسجلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View Card List -->
            <div class="block md:hidden space-y-3">
                @forelse($sales as $sale)
                    <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-mono font-bold text-white">{{ $sale->invoice_number }}</span>
                            @if($sale->status === 'completed')
                                <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مكتملة</span>
                            @else
                                <span class="px-2 py-0.5 text-[9px] rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">ملغاة</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400"><i class="fa-solid fa-user ml-1 text-[9px]"></i> العميل: {{ $sale->customer->name ?? 'عميل نقدي عام' }}</p>
                        
                        <div class="flex justify-between items-center pt-2 border-t border-white/5 text-[10px]">
                            <div>
                                <span class="text-gray-500 block">الإجمالي النهائي</span>
                                <span class="text-emerald-400 font-bold font-mono text-xs">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                            </div>
                            <div class="flex gap-1.5">
                                <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white/5 border border-white/10 text-white rounded-lg">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="p-1.5 bg-[#D41414]/15 border border-[#D41414]/30 text-[#D41414] rounded-lg">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 text-xs">لا توجد فواتير مبيعات مسجلة حالياً.</div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>