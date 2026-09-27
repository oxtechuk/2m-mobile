<x-app-layout>
    <div class="space-y-6">

        <!-- Header Panel -->
        <div class="glass-panel p-4 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-amber-500/10 border-2 border-amber-500/30 flex items-center justify-center text-amber-400 font-extrabold text-2xl overflow-hidden shadow-inner">
                    @if($rep->avatar)
                        <img src="{{ asset($rep->avatar) }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($rep->name, 0, 1) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <span>{{ $rep->name }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            {{ $rep->role === 'admin' ? 'المدير' : ($rep->role === 'cashier' ? 'كاشير / مبيعات' : 'مندوب مبيعات') }}
                        </span>
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">الفرع: {{ $rep->branch->name ?? 'جميع الفروع' }} - هاتف: {{ $rep->phone ?? 'غير مسجل' }}</p>
                </div>
            </div>

            <!-- Month Filter & Back Button -->
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('sales-reps.show', $rep->id) }}">
                    <input 
                        type="month" 
                        name="month" 
                        value="{{ $selectedMonth }}" 
                        onchange="this.form.submit()"
                        class="px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-amber-400 cursor-pointer"
                    >
                </form>

                <a href="{{ route('sales-reps.index') }}?month={{ $selectedMonth }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                    لوحة المندوبين <i class="fa-solid fa-arrow-left mr-1"></i>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Sales -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي المبيعات المحققة</span>
                    <span class="text-xl font-black text-amber-400 font-mono mt-1 block">{{ number_format($totalSalesAmount, 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>

            <!-- Total Invoices -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">عدد الفواتير الناجحة</span>
                    <span class="text-xl font-black text-white font-mono mt-1 block">{{ number_format($salesCount) }} فاتورة</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>

            <!-- Commission Rate -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">نسبة العمولة المعتمدة</span>
                    <span class="text-xl font-black text-indigo-400 font-mono mt-1 block">{{ number_format($commissionRate, 2) }}%</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-percent"></i>
                </div>
            </div>

            <!-- Total Earned Commission -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي العمولة المستحقة بالشهر</span>
                    <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">{{ number_format($earnedCommission, 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>

        <!-- Daily Sales Performance Trend Table -->
        <div class="glass-panel p-4 space-y-3">
            <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-amber-400"></i>
                <span>التوزيع اليومي للمبيعات خلال الشهر (Daily Trend)</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                        <tr>
                            <th class="p-3">التاريخ</th>
                            <th class="p-3 text-center">عدد الفواتير</th>
                            <th class="p-3">إجمالي المبيعات اليومية</th>
                            <th class="p-3">العمولة اليومية المقدرة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($dailySales as $day)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-3 font-mono text-gray-300">{{ $day->date }}</td>
                                <td class="p-3 text-center font-mono font-bold text-white">{{ $day->daily_count }}</td>
                                <td class="p-3 font-mono font-bold text-amber-400">{{ number_format($day->daily_total, 2) }} ج.م</td>
                                <td class="p-3 font-mono font-bold text-emerald-400">{{ number_format($day->daily_total * ($commissionRate / 100), 2) }} ج.م</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">لا توجد حركات بيع يومية مسجلة خلال هذا الشهر.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Invoices Table -->
        <div class="glass-panel p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-white/5 pb-2">
                <h3 class="text-xs font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-indigo-400"></i>
                    <span>سجل فواتير البيع المنفذة بواسطة المندوب</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                        <tr>
                            <th class="p-3">رقم الفاتورة</th>
                            <th class="p-3">العميل</th>
                            <th class="p-3">الفرع</th>
                            <th class="p-3">التاريخ والوقت</th>
                            <th class="p-3">طريقة الدفع</th>
                            <th class="p-3">إجمالي الفاتورة</th>
                            <th class="p-3">العمولة المحسوبة</th>
                            <th class="p-3 text-center">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-3 font-mono font-bold text-indigo-400">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="hover:underline">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>

                                <td class="p-3 text-white">
                                    {{ $sale->customer->name ?? 'عميل نقدي' }}
                                </td>

                                <td class="p-3 text-gray-300">
                                    {{ $sale->branch->name ?? '' }}
                                </td>

                                <td class="p-3 font-mono text-gray-400 text-[11px]">
                                    {{ $sale->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="p-3 text-gray-300">
                                    {{ $sale->payment_method }}
                                </td>

                                <td class="p-3 font-mono font-bold text-white text-sm">
                                    {{ number_format($sale->total, 2) }} ج.م
                                </td>

                                <td class="p-3 font-mono font-bold text-emerald-400">
                                    {{ number_format($sale->total * ($commissionRate / 100), 2) }} ج.م
                                </td>

                                <td class="p-3 text-center">
                                    <a 
                                        href="{{ route('sales.show', $sale->id) }}" 
                                        class="px-2.5 py-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg text-xs transition inline-flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>عرض الفاتورة</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500">لا توجد فواتير مبيعات مسجلة لهذا المندوب في هذا الشهر.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
                <div class="pt-3 border-t border-white/5 bg-[#0a0a0a]">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
