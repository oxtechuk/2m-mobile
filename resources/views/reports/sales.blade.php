<x-app-layout>
    @php
        $currency = setting('default_currency', 'ج.م');
    @endphp

    <div class="space-y-6" x-data="{ activeTab: '{{ request('tab', 'pnl') }}' }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 p-5 rounded-2xl shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-[#D41414]"></i>
                    <span>مركز التقارير والتحليلات المالية والتشغيلية</span>
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    تقرير شامل للفترة من <strong class="text-gray-900 dark:text-white font-mono">{{ $fromDate->format('Y-m-d') }}</strong> إلى <strong class="text-gray-900 dark:text-white font-mono">{{ $toDate->format('Y-m-d') }}</strong>
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-800 dark:text-white rounded-xl transition text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>طباعة التقرير</span>
                </button>
            </div>
        </div>

        <!-- Period & Date Range Filter Bar -->
        <div class="glass-panel p-4 rounded-2xl space-y-3">
            <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <input type="hidden" name="tab" x-model="activeTab">

                <!-- Quick Period Filter Buttons -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <a href="{{ route('reports.sales', ['period' => 'today', 'tab' => request('tab', 'pnl')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ $period === 'today' ? 'bg-[#D41414] text-white shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                        اليوم
                    </a>
                    <a href="{{ route('reports.sales', ['period' => 'yesterday', 'tab' => request('tab', 'pnl')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ $period === 'yesterday' ? 'bg-[#D41414] text-white shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                        أمس
                    </a>
                    <a href="{{ route('reports.sales', ['period' => 'this_week', 'tab' => request('tab', 'pnl')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ $period === 'this_week' ? 'bg-[#D41414] text-white shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                        هذا الأسبوع
                    </a>
                    <a href="{{ route('reports.sales', ['period' => 'this_month', 'tab' => request('tab', 'pnl')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ $period === 'this_month' ? 'bg-[#D41414] text-white shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                        هذا الشهر
                    </a>
                    <a href="{{ route('reports.sales', ['period' => 'last_month', 'tab' => request('tab', 'pnl')]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ $period === 'last_month' ? 'bg-[#D41414] text-white shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                        الشهر الماضي
                    </a>
                </div>

                <!-- Date Inputs for Custom Filter -->
                <div class="flex items-center gap-2 shrink-0 text-xs">
                    <input type="hidden" name="period" value="custom">
                    <input type="date" name="from_date" value="{{ request('from_date', $fromDate->format('Y-m-d')) }}" class="bg-gray-50 dark:bg-[#050505] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1.5 text-gray-900 dark:text-white text-xs">
                    <span class="text-gray-400">إلى</span>
                    <input type="date" name="to_date" value="{{ request('to_date', $toDate->format('Y-m-d')) }}" class="bg-gray-50 dark:bg-[#050505] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1.5 text-gray-900 dark:text-white text-xs">
                    <button type="submit" class="px-3 py-1.5 bg-[#D41414] text-white rounded-xl font-bold shadow hover:bg-[#A30F0F] transition">تطبيق</button>
                </div>
            </form>
        </div>

        <!-- Multi-Tab Navigation Bar -->
        <div class="flex border-b border-gray-200 dark:border-white/10 overflow-x-auto scrollbar-none text-xs font-bold shrink-0 gap-1">
            <button 
                @click="activeTab = 'pnl'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'pnl' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-[#D41414] fa-scale-balanced"></i>
                <span>صافي الأرباح والخسائر</span>
            </button>

            <button 
                @click="activeTab = 'sales'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'sales' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-cart-shopping"></i>
                <span>المبيعات والأصناف</span>
            </button>

            <button 
                @click="activeTab = 'expenses'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'expenses' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>المصروفات والخسائر</span>
            </button>

            <button 
                @click="activeTab = 'maintenance'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'maintenance' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>ورشة الصيانة والأجهزة</span>
            </button>

            <button 
                @click="activeTab = 'shifts'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'shifts' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-vault"></i>
                <span>الورديات والخزائن</span>
            </button>

            <button 
                @click="activeTab = 'transfers'" 
                class="py-3 px-4 transition border-b-2 flex items-center gap-2 shrink-0"
                :class="activeTab === 'transfers' ? 'border-[#D41414] text-[#D41414] bg-[#D41414]/10 rounded-t-xl' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
            >
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                <span>التحويلات المالية</span>
            </button>
        </div>

        <!-- Tab 1: Net Profit & Loss (P&L) Dashboard -->
        <div x-show="activeTab === 'pnl'" class="space-y-6">
            <!-- Big Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="glass-panel p-5 flex flex-col justify-between">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">إجمالي الإيرادات (المبيعات والصيانة)</span>
                    <span class="text-2xl font-black text-emerald-500 dark:text-emerald-400 font-mono">{{ number_format($grossRevenue, 2) }} {{ $currency }}</span>
                    <p class="text-[10px] text-gray-400">مجموع المبيعات + تسليمات الصيانة</p>
                </div>

                <div class="glass-panel p-5 flex flex-col justify-between">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">تكلفة البضاعة المباعة (COGS)</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ number_format($cogs, 2) }} {{ $currency }}</span>
                    <p class="text-[10px] text-gray-400">إجمالي سعر تكلفة المنتجات المباعة</p>
                </div>

                <div class="glass-panel p-5 flex flex-col justify-between">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">إجمالي المصروفات التشغيلية</span>
                    <span class="text-2xl font-black text-rose-500 font-mono">{{ number_format($totalExpenses, 2) }} {{ $currency }}</span>
                    <p class="text-[10px] text-gray-400">الفواتير، الإيجارات، والرواتب</p>
                </div>

                <div class="glass-panel p-5 flex flex-col justify-between border-2 {{ $netProfit >= 0 ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-rose-500/40 bg-rose-500/5' }}">
                    <span class="text-xs font-bold text-gray-900 dark:text-white">صافي الأرباح / الخسائر للفترة</span>
                    <span class="text-2xl font-black font-mono {{ $netProfit >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500' }}">
                        {{ number_format($netProfit, 2) }} {{ $currency }}
                    </span>
                    <p class="text-[10px] font-bold {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                        هامش الربحية: {{ number_format($profitMargin, 1) }}%
                    </p>
                </div>
            </div>

            <!-- Financial Breakdown Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="glass-panel p-5 space-y-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-white/10 pb-2">تحليل الهيكل المالي</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-white/5">
                            <span class="text-gray-600 dark:text-gray-300">مبيعات المنتجات الإجمالية:</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ number_format($totalSales, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-white/5">
                            <span class="text-gray-600 dark:text-gray-300">إيرادات خدمات ورشة الصيانة:</span>
                            <span class="font-mono font-bold text-emerald-500 dark:text-emerald-400">{{ number_format($maintenanceRevenues, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-white/5">
                            <span class="text-gray-600 dark:text-gray-300">تكلفة البضاعة المباعة الأصلية:</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">- {{ number_format($cogs, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-white/5">
                            <span class="text-gray-600 dark:text-gray-300">إجمالي المصروفات المخصومة:</span>
                            <span class="font-mono font-bold text-rose-500">- {{ number_format($totalExpenses, 2) }} {{ $currency }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-5 space-y-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-white/10 pb-2">توزيع طرق تحصيل المبيعات</h3>
                    <div class="space-y-4 pt-2">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-600 dark:text-gray-300">نقدي (كاش):</span>
                                <span class="font-bold font-mono text-gray-900 dark:text-white">{{ number_format($cashSales, 2) }} {{ $currency }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-white/10 h-2.5 rounded-full overflow-hidden">
                                @php $cashPct = $totalSales > 0 ? ($cashSales / $totalSales) * 100 : 0; @endphp
                                <div class="bg-[#D41414] h-full rounded-full" style="width: {{ $cashPct }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-600 dark:text-gray-300">فيزا / محافظ إلكترونية:</span>
                                <span class="font-bold font-mono text-gray-900 dark:text-white">{{ number_format($otherSales, 2) }} {{ $currency }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-white/10 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full" style="width: {{ 100 - $cashPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Sales & Top Selling Products -->
        <div x-show="activeTab === 'sales'" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المبيعات</span>
                    <h3 class="text-2xl font-black text-emerald-500 dark:text-emerald-400 font-mono">{{ number_format($totalSales, 2) }} {{ $currency }}</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد الفواتير الصادرة</span>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ $salesCount }} فاتورة</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">متوسط قيمة الفاتورة</span>
                    <h3 class="text-2xl font-black text-blue-500 dark:text-blue-400 font-mono">{{ number_format($averageTicket, 2) }} {{ $currency }}</h3>
                </div>
            </div>

            <!-- Top Products Table -->
            <div class="glass-panel p-5 space-y-3">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-white/10 pb-2">المنتجات الأكثر مبيعاً في هذه الفترة</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-100 dark:bg-white/5 font-bold">
                            <tr>
                                <th class="py-2.5 px-3">اسم المنتج</th>
                                <th class="py-2.5 px-3">القسم</th>
                                <th class="py-2.5 px-3">الكمية المباعة</th>
                                <th class="py-2.5 px-3">إجمالي الإيراد</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($topProducts as $item)
                                <tr>
                                    <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white">{{ $item->product->name ?? 'منتج محذوف' }}</td>
                                    <td class="py-2.5 px-3">{{ $item->product->category->name ?? 'عام' }}</td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-emerald-500">{{ $item->total_qty }} قطعة</td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-gray-900 dark:text-white">{{ number_format($item->total_amount, 2) }} {{ $currency }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400">لا توجد مبيعات مسجلة خلال الفترة المحددة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 3: Expenses Breakdown -->
        <div x-show="activeTab === 'expenses'" class="space-y-6">
            <div class="glass-panel p-5 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">تفاصيل توزيع المصروفات حسب التصنيف</h3>
                    <span class="text-xs font-bold text-rose-500 font-mono">الإجمالي: {{ number_format($totalExpenses, 2) }} {{ $currency }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($expensesByCategory as $expCat)
                        <div class="p-4 bg-gray-50 dark:bg-[#050505] rounded-xl border border-gray-200 dark:border-white/10 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $expCat->category }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $expCat->count }} عملية صرف</p>
                            </div>
                            <span class="text-sm font-black text-rose-500 font-mono">{{ number_format($expCat->total_amount, 2) }} {{ $currency }}</span>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-gray-400">لا توجد مصروفات مسجلة في هذه الفترة.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tab 4: Maintenance Reports -->
        <div x-show="activeTab === 'maintenance'" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي تذاكر الصيانة</span>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ $totalTickets }} تذكرة</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">أجهزة جاري صلحها</span>
                    <h3 class="text-2xl font-black text-amber-500 font-mono">{{ $inProgressTickets }} جهاز</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">أجهزة تم تسليمها للعميل</span>
                    <h3 class="text-2xl font-black text-emerald-500 font-mono">{{ $deliveredTickets }} جهاز</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إيرادات خدمات الصيانة</span>
                    <h3 class="text-2xl font-black text-blue-500 font-mono">{{ number_format($maintenanceRevenues, 2) }} {{ $currency }}</h3>
                </div>
            </div>
        </div>

        <!-- Tab 5: Shifts & Vault Reports -->
        <div x-show="activeTab === 'shifts'" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد الورديات المفتوحة/المغلقة</span>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ $shiftsCount }} وردية</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">صافي فروقات العجز/الزيادة للكاشير</span>
                    <h3 class="text-2xl font-black font-mono {{ $totalShiftDifferences >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ number_format($totalShiftDifferences, 2) }} {{ $currency }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Tab 6: Money Transfers Reports -->
        <div x-show="activeTab === 'transfers'" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد التحويلات المالية الصادرة</span>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">{{ $transfersCount }} تحويل</h3>
                </div>
                <div class="glass-panel p-5 space-y-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المبالغ المحولة</span>
                    <h3 class="text-2xl font-black text-blue-500 font-mono">{{ number_format($totalTransferredAmount, 2) }} {{ $currency }}</h3>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>