<x-app-layout>
    <div class="space-y-6" x-data="{ rateModalOpen: false, activeRepId: null, activeRepName: '', activeRate: 0 }">

        <!-- Top Header & Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-amber-500 dark:text-amber-400"></i>
                    <span>أداء ومندوبي المبيعات وترتيب الأفضل (Leaderboard)</span>
                </h1>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">متابعة مبيعات الموظفين الشهرية، احتساب النسبة والعمولات المستحقة، وتتويج أبطال المبيعات.</p>
            </div>

            <!-- Month & Branch Filter Form -->
            <form method="GET" action="{{ route('sales-reps.index') }}" class="flex flex-wrap items-center gap-2">
                <div>
                    <input 
                        type="month" 
                        name="month" 
                        value="{{ $selectedMonth }}" 
                        onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-100 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl text-gray-900 dark:text-white font-mono text-xs focus:outline-none focus:border-amber-400 cursor-pointer"
                    >
                </div>

                <div>
                    <select 
                        name="branch_id" 
                        onchange="this.form.submit()"
                        class="px-3 py-2 bg-gray-100 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl text-gray-900 dark:text-white text-xs focus:outline-none focus:border-amber-400 cursor-pointer"
                    >
                        <option value="all">جميع الفروع</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- 🏆 TOP 3 SALES REPS PODIUM (منصة تتويج الأفضل في الشهر) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            
            <!-- 🥈 2nd Place (Silver) -->
            <div class="glass-panel p-6 border-t-4 border-t-slate-400 text-center relative overflow-hidden bg-slate-500/10 dark:bg-gradient-to-b dark:from-slate-500/10 dark:to-transparent order-2 md:order-1 transform hover:-translate-y-1 transition duration-300">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-slate-400 text-black font-extrabold text-[10px] rounded-full shadow-lg flex items-center gap-1">
                    <i class="fa-solid fa-medal"></i> المركز الثاني 🥈
                </div>
                
                <div class="mt-4 flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full border-2 border-slate-400 p-1 relative bg-slate-200 dark:bg-white/5">
                        <div class="w-full h-full rounded-full bg-slate-200 dark:bg-slate-500/20 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold text-xl overflow-hidden">
                            @if($top2 && $top2->avatar)
                                <img src="{{ asset($top2->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($top2->name ?? '؟', 0, 1) }}
                            @endif
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-900 dark:text-white text-sm mt-3">{{ $top2->name ?? 'غير محدد' }}</h3>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400">{{ $top2->branch_name ?? '' }}</span>

                    <div class="mt-3 bg-gray-50 dark:bg-[#0a0a0a] p-2.5 rounded-xl w-full border border-gray-200 dark:border-white/5 space-y-1">
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 block">إجمالي مبيعات الشهر</span>
                        <span class="text-base font-black font-mono text-slate-800 dark:text-slate-300 block">{{ number_format($top2->total_sales ?? 0, 2) }} ج.م</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono block">العمولة: {{ number_format($top2->earned_commission ?? 0, 2) }} ج.م</span>
                    </div>
                </div>
            </div>

            <!-- 🥇 1st Place (Gold Champion) -->
            <div class="glass-panel p-6 border-2 border-amber-400 border-t-8 border-t-amber-400 text-center relative overflow-hidden bg-amber-500/10 dark:bg-gradient-to-b dark:from-amber-500/20 dark:via-amber-500/5 dark:to-transparent order-1 md:order-2 shadow-2xl transform hover:-translate-y-2 transition duration-300">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-amber-400 text-amber-950 font-black text-xs rounded-full shadow-xl flex items-center gap-1">
                    <i class="fa-solid fa-crown text-amber-900"></i> بطل المبيعات 🥇
                </div>
                
                <div class="mt-4 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-full border-4 border-amber-400 p-1 relative bg-amber-200/60 dark:bg-amber-500/20 shadow-lg">
                        <div class="w-full h-full rounded-full bg-amber-300/40 dark:bg-amber-500/30 flex items-center justify-center text-amber-900 dark:text-amber-300 font-extrabold text-2xl overflow-hidden">
                            @if($top1 && $top1->avatar)
                                <img src="{{ asset($top1->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($top1->name ?? '؟', 0, 1) }}
                            @endif
                        </div>
                    </div>

                    <h3 class="font-extrabold text-gray-900 dark:text-white text-base mt-3 flex items-center gap-1">
                        <span>{{ $top1->name ?? 'غير محدد' }}</span>
                        <i class="fa-solid fa-circle-check text-amber-500 text-xs"></i>
                    </h3>
                    <span class="text-xs text-amber-800 dark:text-amber-300/80 font-semibold">{{ $top1->branch_name ?? '' }}</span>

                    <div class="mt-4 bg-amber-500/10 dark:bg-[#0a0a0a] p-3 rounded-xl w-full border border-amber-400/30 dark:border-amber-500/30 space-y-1">
                        <span class="text-[11px] text-amber-950 dark:text-gray-300 block font-bold">إجمالي مبيعات الشهر</span>
                        <span class="text-xl font-black font-mono text-amber-600 dark:text-amber-400 block">{{ number_format($top1->total_sales ?? 0, 2) }} ج.م</span>
                        <div class="flex justify-between items-center text-[10px] text-gray-600 dark:text-gray-400 pt-1 border-t border-amber-200 dark:border-white/5">
                            <span>الفواتير: <strong class="text-gray-900 dark:text-white font-mono">{{ $top1->sales_count ?? 0 }}</strong></span>
                            <span>العمولة: <strong class="text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($top1->earned_commission ?? 0, 2) }} ج.م</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🥉 3rd Place (Bronze) -->
            <div class="glass-panel p-6 border-t-4 border-t-amber-700 text-center relative overflow-hidden bg-amber-900/10 dark:bg-gradient-to-b dark:from-amber-900/10 dark:to-transparent order-3 transform hover:-translate-y-1 transition duration-300">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-amber-700 text-white font-extrabold text-[10px] rounded-full shadow-lg flex items-center gap-1">
                    <i class="fa-solid fa-medal"></i> المركز الثالث 🥉
                </div>
                
                <div class="mt-4 flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full border-2 border-amber-700 p-1 relative bg-amber-100 dark:bg-white/5">
                        <div class="w-full h-full rounded-full bg-amber-200/50 dark:bg-amber-900/20 flex items-center justify-center text-amber-800 dark:text-amber-600 font-bold text-xl overflow-hidden">
                            @if($top3 && $top3->avatar)
                                <img src="{{ asset($top3->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr($top3->name ?? '؟', 0, 1) }}
                            @endif
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-900 dark:text-white text-sm mt-3">{{ $top3->name ?? 'غير محدد' }}</h3>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400">{{ $top3->branch_name ?? '' }}</span>

                    <div class="mt-3 bg-gray-50 dark:bg-[#0a0a0a] p-2.5 rounded-xl w-full border border-gray-200 dark:border-white/5 space-y-1">
                        <span class="text-[10px] text-gray-600 dark:text-gray-400 block">إجمالي مبيعات الشهر</span>
                        <span class="text-base font-black font-mono text-amber-700 dark:text-amber-600 block">{{ number_format($top3->total_sales ?? 0, 2) }} ج.م</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono block">العمولة: {{ number_format($top3->earned_commission ?? 0, 2) }} ج.م</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Metric Stat Cards (Overall Team Totals) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Team Total Sales -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400 block font-semibold">إجمالي مبيعات الفريق بالشهر</span>
                    <span class="text-xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1 block">{{ number_format($teamStats['total_sales'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <!-- Team Total Invoices -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400 block font-semibold">إجمالي عدد الفواتير الناجحة</span>
                    <span class="text-xl font-black text-gray-900 dark:text-white font-mono mt-1 block">{{ number_format($teamStats['total_invoices']) }} فاتورة</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <!-- Total Earned Commissions -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400 block font-semibold">إجمالي عمولات الفريق المحسوبة</span>
                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">{{ number_format($teamStats['total_commissions'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>

            <!-- Top Performer -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400 block font-semibold">أفضل مندوب في الشهر</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white mt-1 block truncate max-w-[150px]">{{ $teamStats['top_seller'] }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>
        </div>

        <!-- Full Sales Representatives Table -->
        <div class="glass-panel overflow-hidden space-y-4 p-4">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-white/5 pb-3">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-users-viewfinder text-amber-500 dark:text-amber-400"></i>
                    <span>جدول أداء وترتيب جميع المندوبين والموظفين</span>
                </h3>
                <span class="text-xs text-gray-600 dark:text-gray-400 font-mono">شهر: {{ $selectedMonth }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-100 dark:bg-[#0a0a0a] text-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 font-bold">
                        <tr>
                            <th class="p-3 text-center w-12"># الترتيب</th>
                            <th class="p-3">المندوب / الموظف</th>
                            <th class="p-3">الفرع</th>
                            <th class="p-3 text-center">عدد الفواتير</th>
                            <th class="p-3">إجمالي المبيعات</th>
                            <th class="p-3">متوسط الفاتورة</th>
                            <th class="p-3 text-center">نسبة العمولة (%)</th>
                            <th class="p-3">العمولة المستحقة</th>
                            <th class="p-3 text-center">التفاصيل والخيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($sortedReps as $index => $rep)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition {{ $index === 0 ? 'bg-amber-500/[0.08] dark:bg-amber-500/[0.04]' : '' }}">
                                <td class="p-3 text-center font-bold font-mono">
                                    @if($index === 0)
                                        <span class="w-7 h-7 rounded-full bg-amber-400 text-black font-black inline-flex items-center justify-center shadow-lg">1</span>
                                    @elseif($index === 1)
                                        <span class="w-7 h-7 rounded-full bg-slate-400 text-black font-bold inline-flex items-center justify-center">2</span>
                                    @elseif($index === 2)
                                        <span class="w-7 h-7 rounded-full bg-amber-700 text-white font-bold inline-flex items-center justify-center">3</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-white/5 border border-gray-300 dark:border-white/10 flex items-center justify-center text-gray-900 dark:text-white font-bold overflow-hidden shrink-0">
                                            @if($rep->avatar)
                                                <img src="{{ asset($rep->avatar) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ mb_substr($rep->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('sales-reps.show', $rep->id) }}?month={{ $selectedMonth }}" class="font-bold text-gray-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition block">
                                                {{ $rep->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-500 font-mono block">{{ $rep->phone ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-3 text-gray-700 dark:text-gray-300">
                                    {{ $rep->branch_name }}
                                </td>

                                <td class="p-3 text-center font-mono font-bold text-gray-900 dark:text-white">
                                    <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                                        {{ number_format($rep->sales_count) }}
                                    </span>
                                </td>

                                <td class="p-4 font-mono font-black text-sm text-gray-900 dark:text-white">
                                    {{ number_format($rep->total_sales, 2) }} ج.م
                                </td>

                                <td class="p-3 font-mono text-gray-700 dark:text-gray-300">
                                    {{ number_format($rep->avg_order_value, 2) }} ج.م
                                </td>

                                <td class="p-3 text-center font-mono">
                                    <button 
                                        type="button" 
                                        @click="activeRepId = {{ $rep->id }}; activeRepName = '{{ $rep->name }}'; activeRate = {{ $rep->commission_rate }}; rateModalOpen = true;"
                                        class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 rounded-lg text-xs font-bold transition flex items-center gap-1 mx-auto cursor-pointer"
                                        title="تعديل نسبة العمولة"
                                    >
                                        <span>{{ number_format($rep->commission_rate, 2) }}%</span>
                                        <i class="fa-solid fa-pen text-[10px] opacity-70"></i>
                                    </button>
                                </td>

                                <td class="p-3 font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ number_format($rep->earned_commission, 2) }} ج.م
                                </td>

                                <td class="p-3 text-center">
                                    <a 
                                        href="{{ route('sales-reps.show', $rep->id) }}?month={{ $selectedMonth }}" 
                                        class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-600/20 hover:bg-indigo-100 dark:hover:bg-indigo-600/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 rounded-lg text-xs font-bold transition inline-flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-chart-pie text-xs"></i>
                                        <span>كشف الحساب والتقرير</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-gray-500">
                                    <i class="fa-solid fa-user-slash text-3xl mb-2 block"></i>
                                    <span>لا يوجد مبيعات مسجلة لمندوبين خلال الشهر المحدد.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commission Rate Update Modal -->
        <div 
            x-show="rateModalOpen" 
            x-transition:opacity 
            class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="glass-panel max-w-md w-full p-6 space-y-4 bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl" @click.stop>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-white/5 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-percent text-amber-500 dark:text-amber-400"></i>
                        <span>تعديل نسبة عمولة المندوب: <span class="text-amber-600 dark:text-amber-400" x-text="activeRepName"></span></span>
                    </h3>
                    <button @click="rateModalOpen = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form :action="`/sales-reps/${activeRepId}/commission-rate`" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1">
                        <label for="commission_rate" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">نسبة العمولة من إجمالي المبيعات (%) <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            max="100" 
                            name="commission_rate" 
                            id="commission_rate" 
                            x-model="activeRate"
                            required 
                            class="block w-full px-3 py-2 bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl text-amber-600 dark:text-amber-400 font-mono text-left font-bold text-sm focus:outline-none focus:border-amber-400"
                        >
                        <p class="text-[10px] text-gray-500">مثال: أدخل 2.5 لحساب عمولة 2.5% من إجمالي الفواتير التي ينفذها المندوب.</p>
                    </div>

                    <div class="pt-3 border-t border-gray-200 dark:border-white/5 flex justify-end gap-2">
                        <button type="button" @click="rateModalOpen = false" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold transition">
                            إلغاء
                        </button>
                        <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-black font-bold rounded-xl text-xs transition shadow-lg">
                            حفظ النسبة الجديدة <i class="fa-solid fa-check mr-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
