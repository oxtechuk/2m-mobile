<x-app-layout>
    @php
        $currency = setting('default_currency', 'ج.م');
    @endphp

    <div class="space-y-6">
        
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 p-5 rounded-2xl shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center space-x-2 space-x-reverse">
                    <span>أهلاً بك، {{ auth()->user()->name }}</span>
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ auth()->user()->role === 'admin' ? 'المدير العام' : 'طاقم العمل' }})</span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    متابعة سريعة لأداء فرع: <strong class="text-[#D41414] dark:text-[#F04848] font-bold">{{ $currentBranch->name ?? 'الفرع الرئيسي' }}</strong>
                </p>
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <i class="fa-regular fa-clock ml-1 text-[#D41414]"></i> آخر تحديث: اليوم {{ date('h:i A') }}
                </span>
                <button onclick="window.location.reload()" class="px-3.5 py-2 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-800 dark:text-white rounded-xl transition text-xs font-bold flex items-center shadow-sm">
                    <i class="fa-solid fa-arrows-rotate ml-1"></i> تحديث القراءات
                </button>
            </div>
        </div>

        <!-- 4 Live Stat Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Live Today Sales Card -->
            <div class="glass-panel p-5 glass-card-hover flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">إجمالي مبيعات اليوم</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">
                        {{ number_format($todaySalesTotal, 2) }} <span class="text-xs font-normal text-gray-400">{{ $currency }}</span>
                    </h3>
                    <p class="text-[10px] font-bold {{ $salesGrowth >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500' }}">
                        <i class="fa-solid {{ $salesGrowth >= 0 ? 'fa-trend-up' : 'fa-trend-down' }}"></i> 
                        {{ number_format(abs($salesGrowth), 1) }}% مقارنة بأمس ({{ $todaySalesCount }} عمليات بيع)
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 dark:text-emerald-400 shadow-sm">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>

            <!-- 2. Live Maintenance Tickets Card -->
            <div class="glass-panel p-5 glass-card-hover flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">طلب صيانة قائم بالورشة</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">
                        {{ $activeMaintenanceCount }} <span class="text-xs font-normal text-gray-400">جهاز</span>
                    </h3>
                    <p class="text-[10px] text-amber-500 dark:text-amber-400 font-bold">
                        <i class="fa-solid fa-clock"></i> {{ $waitingPartsCount }} بانتظار قطع الغيار
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm">
                    <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                </div>
            </div>

            <!-- 3. Live Customers Card -->
            <div class="glass-panel p-5 glass-card-hover flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">إجمالي عملاء النظام</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">
                        {{ $totalCustomersCount }} <span class="text-xs font-normal text-gray-400">عميل</span>
                    </h3>
                    <p class="text-[10px] text-[#D41414] font-bold">
                        <i class="fa-solid fa-user-plus"></i> +{{ $todayNewCustomersCount }} عميل جديد تم تسجيلهم اليوم
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-[#D41414]/10 border border-[#D41414]/20 flex items-center justify-center text-[#D41414] shadow-sm">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>

            <!-- 4. Live Cash Shift / Vault Balance Card -->
            <div class="glass-panel p-5 glass-card-hover flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">رصيد الخزينة / الوردية</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white font-mono">
                        {{ number_format($vaultBalance, 2) }} <span class="text-xs font-normal text-gray-400">{{ $currency }}</span>
                    </h3>
                    <p class="text-[10px] font-bold {{ $activeShift ? 'text-emerald-500' : 'text-gray-400' }}">
                        <i class="fa-solid {{ $activeShift ? 'fa-lock-open' : 'fa-lock' }}"></i> 
                        {{ $activeShift ? 'الوردية مفتوحة للبيع' : 'لا توجد وردية مفتوحة' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 dark:text-blue-400 shadow-sm">
                    <i class="fa-solid fa-vault text-xl"></i>
                </div>
            </div>

        </div>

        <!-- Dynamic Charts & Low Stock Alerts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Dynamic Weekly Sales Chart -->
            <div class="glass-panel p-5 lg:col-span-2 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-chart-area text-[#D41414] ml-2"></i>
                        <span>منحنى المبيعات الأسبوعي الفعلي</span>
                    </h3>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 px-2.5 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">آخر 7 أيام</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Realtime Low Stock Alerts Panel -->
            <div class="glass-panel p-5 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 ml-2"></i>
                        <span>تنبيهات نواقص المخزن الحية</span>
                    </h3>
                    <span class="text-[10px] bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 font-bold px-2 py-0.5 rounded">هام جدًا</span>
                </div>
                
                <div class="space-y-2.5 overflow-y-auto max-h-64 pr-1">
                    @forelse($lowStockProducts as $prod)
                        <div class="p-3 bg-gray-50 dark:bg-[#050505] rounded-xl border border-gray-200 dark:border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $prod->name }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">القسم: {{ $prod->category->name ?? 'عام' }}</p>
                            </div>
                            <div class="text-left">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $prod->stock_quantity <= 0 ? 'bg-rose-500/20 text-rose-500 border border-rose-500/30' : 'bg-amber-500/20 text-amber-500 border border-amber-500/30' }}">
                                    {{ $prod->stock_quantity <= 0 ? 'نفد بالكامل' : 'المتبقي: ' . $prod->stock_quantity . ' قطع' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-xs text-gray-400">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-3xl mb-2 block"></i>
                            <p class="font-bold text-gray-600 dark:text-gray-300">جميع المنتجات متوفرة وبكميات ممتازة</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Recent Invoices Table -->
        <div class="glass-panel p-5 space-y-4">
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-white/10 pb-3">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fa-solid fa-receipt text-[#D41414] ml-2"></i>
                    <span>أحدث المبيعات والفواتير المسجلة في الفرع</span>
                </h3>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-[#D41414] hover:underline flex items-center gap-1">
                    <span>عرض كافة الفواتير</span>
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-right text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 uppercase font-bold border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="py-3 px-4">رقم الفاتورة</th>
                            <th class="py-3 px-4">العميل</th>
                            <th class="py-3 px-4">طريقة الدفع</th>
                            <th class="py-3 px-4">إجمالي المبلغ</th>
                            <th class="py-3 px-4">التاريخ والوقت</th>
                            <th class="py-3 px-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="py-3 px-4 font-mono font-bold text-gray-900 dark:text-white">#{{ $sale->invoice_number }}</td>
                                <td class="py-3 px-4 font-medium">{{ $sale->customer->name ?? 'عميل نقدي عام' }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 border border-blue-500/20 text-blue-500">
                                        {{ $sale->payment_method === 'cash' ? 'نقدي (Cash)' : $sale->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($sale->total, 2) }} {{ $currency }}
                                </td>
                                <td class="py-3 px-4 text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('Y-m-d h:i A') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white/10 hover:bg-[#D41414] text-gray-700 dark:text-white rounded-lg transition inline-flex items-center" title="معاينة الفاتورة">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    لا توجد فواتير بيع مسجلة بالفرع اليوم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ChartJS Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            const labels = @json($chartLabels);
            const salesData = @json($chartSalesData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'إجمالي المبيعات ({{ $currency }})',
                        data: salesData,
                        borderColor: '#D41414',
                        backgroundColor: 'rgba(212, 20, 20, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#D41414',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: document.documentElement.classList.contains('light') ? '#1f2937' : '#e5e7eb',
                                font: { family: 'Cairo', size: 12, weight: 'bold' }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: {
                                color: document.documentElement.classList.contains('light') ? '#4b5563' : '#9ca3af',
                                font: { family: 'Cairo', size: 11 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: {
                                color: document.documentElement.classList.contains('light') ? '#4b5563' : '#9ca3af',
                                font: { family: 'Cairo', size: 11 }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
