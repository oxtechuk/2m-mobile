<x-app-layout>
    <div class="space-y-6" x-data="{ period: '{{ $period }}', showCustomDates: '{{ $period }}' === 'custom' }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-[#D41414]"></i>
                    <span>فواتير وسجل المبيعات</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">مراجعة والبحث في الفواتير وإدارتها والتصفية بالفترة الزمنية.</p>
            </div>
            @can('create-sale')
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-xl transition shadow-lg flex items-center gap-1.5 glow-primary">
                <i class="fa-solid fa-cash-register"></i> فاتورة بيع جديدة (POS)
            </a>
            @endcan
        </div>

        <!-- Stat Metric Cards for Filtered Period -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Filtered Sales -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي المبيعات (الفترة المحددة)</span>
                    <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">{{ number_format($stats['total_amount'], 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>

            <!-- Total Invoices Count -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">عدد الفواتير الناجحة</span>
                    <span class="text-xl font-black text-white font-mono mt-1 block">{{ number_format($stats['total_count']) }} فاتورة</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <!-- Cash Sales -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">المبيعات النقدية (كاش)</span>
                    <span class="text-xl font-black text-amber-400 font-mono mt-1 block">{{ number_format($stats['cash_sales'], 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>

            <!-- Other/Card Sales -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">شبكة / محفظة / أخرى</span>
                    <span class="text-xl font-black text-purple-400 font-mono mt-1 block">{{ number_format($stats['other_sales'], 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
            </div>
        </div>

        <!-- Filter Bar & Search Form -->
        <div class="glass-panel p-4 space-y-4">
            <form method="GET" action="{{ route('sales.index') }}" id="salesFilterForm" class="space-y-4">
                
                <!-- Date Period Quick Selector Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button 
                        type="submit" 
                        name="period" 
                        value="today"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="period === 'today' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-calendar-day text-[11px]"></i>
                        <span>اليوم (الافتراضي)</span>
                    </button>

                    <button 
                        type="submit" 
                        name="period" 
                        value="yesterday"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="period === 'yesterday' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                        <span>أمس</span>
                    </button>

                    <button 
                        type="submit" 
                        name="period" 
                        value="this_week"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="period === 'this_week' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-calendar-week text-[11px]"></i>
                        <span>هذا الأسبوع</span>
                    </button>

                    <button 
                        type="submit" 
                        name="period" 
                        value="this_month"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="period === 'this_month' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-calendar-days text-[11px]"></i>
                        <span>هذا الشهر</span>
                    </button>

                    <button 
                        type="button" 
                        @click="showCustomDates = !showCustomDates; period = 'custom';"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="showCustomDates || period === 'custom' ? 'bg-amber-600 text-white shadow-md' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-sliders text-[11px]"></i>
                        <span>تاريخ محدد / من - إلى</span>
                    </button>

                    <button 
                        type="submit" 
                        name="period" 
                        value="all"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
                        :class="period === 'all' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-white/5 border border-white/10 text-gray-300 hover:bg-white/10'"
                    >
                        <i class="fa-solid fa-infinity text-[11px]"></i>
                        <span>جميع الأوقات</span>
                    </button>
                </div>

                <!-- Custom Date Inputs (Shown if custom is picked) -->
                <div x-show="showCustomDates || period === 'custom'" x-collapse class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-white/5">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-300 mb-1">من تاريخ:</label>
                        <input 
                            type="date" 
                            name="from_date" 
                            value="{{ request('from_date', optional($fromDate)->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-[#D41414]"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-300 mb-1">إلى تاريخ:</label>
                        <input 
                            type="date" 
                            name="to_date" 
                            value="{{ request('to_date', optional($toDate)->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-[#D41414]"
                        >
                    </div>

                    <div class="flex items-end">
                        <button type="submit" name="period" value="custom" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition">
                            تطبيق النطاق <i class="fa-solid fa-check mr-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Search Input Bar -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-white/5">
                    <div class="flex-1 relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="البحث برقم الفاتورة أو اسم العميل أو الهاتف..." 
                            value="{{ request('search') }}"
                            class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414]"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-2 bg-white/10 border border-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">
                            بحث <i class="fa-solid fa-magnifying-glass mr-1"></i>
                        </button>

                        @if(request()->hasAny(['search', 'period', 'from_date', 'to_date']))
                            <a href="{{ route('sales.index') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-400 rounded-xl text-xs font-bold transition">
                                إعادة ضبط <i class="fa-solid fa-rotate-left mr-1"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </form>
        </div>

        <!-- Invoices List -->
        <div class="glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <span class="text-xs font-bold text-gray-300">
                    قائمة الفواتير 
                    @if($period === 'today') (اليوم {{ now()->format('Y-m-d') }}) @endif
                </span>
                <span class="text-xs text-gray-400 font-mono font-bold">{{ $sales->count() }} فاتورة</span>
            </div>

            <!-- Desktop View Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-white/5 pb-2 font-bold">
                            <th class="pb-3">رقم الفاتورة</th>
                            <th class="pb-3">التاريخ والوقت</th>
                            <th class="pb-3">العميل</th>
                            <th class="pb-3">الكاشير</th>
                            <th class="pb-3">طريقة الدفع</th>
                            <th class="pb-3">الفرع</th>
                            <th class="pb-3">الإجمالي النهائي</th>
                            <th class="pb-3">الحالة</th>
                            <th class="pb-3 text-center">خيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-mono font-bold text-white">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="hover:text-[#D41414]">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-3 font-mono text-gray-400 text-[11px]">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 text-gray-300">{{ $sale->customer->name ?? 'عميل نقدي عام' }}</td>
                                <td class="py-3 text-gray-300">{{ $sale->user->name }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-white/5 border border-white/10 font-mono">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-400">{{ $sale->branch->name ?? 'المركز الرئيسي' }}</td>
                                <td class="py-3 font-bold text-emerald-400 font-mono text-sm">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="py-3">
                                    @if($sale->status === 'completed')
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مكتملة</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">ملغاة</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg transition" title="تفاصيل الفاتورة">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="p-1.5 bg-[#D41414]/10 hover:bg-[#D41414]/20 text-[#D41414] rounded-lg transition" title="طباعة الفاتورة">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-500">
                                    <i class="fa-solid fa-file-excel text-3xl mb-2 block"></i>
                                    <span>لا توجد فواتير مبيعات مسجلة في هذه الفترة الزمنية.</span>
                                </td>
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
                                <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مكتملة</span>
                            @else
                                <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">ملغاة</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-gray-400"><i class="fa-solid fa-user ml-1 text-[9px]"></i> العميل: {{ $sale->customer->name ?? 'عميل نقدي عام' }}</p>
                        <p class="text-[10px] text-gray-500 font-mono">{{ $sale->created_at->format('Y-m-d H:i') }}</p>
                        
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
                    <div class="py-8 text-center text-gray-500 text-xs">لا توجد فواتير مبيعات مسجلة في هذه الفترة.</div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>