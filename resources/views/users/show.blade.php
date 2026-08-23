<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="glass-panel p-5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-700 dark:text-gray-300 text-2xl font-bold overflow-hidden shrink-0 shadow-inner">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user-tie text-[#D41414]"></i>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-black text-gray-900 dark:text-white">{{ $user->name }}</h1>
                        @if($user->is_active)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold">نشط</span>
                        @else
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-500 font-bold">معطل</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 flex items-center gap-3 mt-1">
                        <span><i class="fa-solid fa-building ml-1 text-[#D41414]"></i>{{ $user->branch->name ?? 'الإدارة العامة' }}</span>
                        <span>•</span>
                        <span><i class="fa-solid fa-briefcase ml-1 text-amber-500"></i>{{ match($user->role) { 'admin' => 'مدير عام', 'branch_manager' => 'مدير فرع', 'cashier' => 'كاشير مبيعات', 'technician' => 'فني صيانة', 'customer_service' => 'خدمة عملاء', default => $user->role } }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Add Advance / Deduction -->
                <a href="{{ route('payroll.adjustments', ['user_id' => $user->id]) }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-zinc-950 font-black text-xs rounded-xl transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>سجل السلف والخصومات</span>
                </a>

                <!-- Edit Employee -->
                <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-bold text-xs rounded-xl transition border border-white/10 flex items-center gap-2">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>تعديل البيانات والراتب</span>
                </a>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            
            <!-- Basic Salary -->
            <div class="glass-panel p-4 space-y-1 border-r-4 border-emerald-500">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400">الراتب الأساسي الشهري</p>
                <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($user->salary ?? 0, 2) }} <span class="text-xs">ج.م</span></p>
                <p class="text-[11px] text-gray-400">يُصرف شهرياً في الموعد المحدد</p>
            </div>

            <!-- Payment Day -->
            <div class="glass-panel p-4 space-y-1 border-r-4 border-blue-500">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400">موعد صرف الراتب الشهري</p>
                <p class="text-xl font-black text-blue-500 font-mono">يوم {{ $user->salary_payment_day ?? 1 }}</p>
                <p class="text-[11px] text-gray-400">من كل شهر ميلادي</p>
            </div>

            <!-- Commission Rate -->
            <div class="glass-panel p-4 space-y-1 border-r-4 border-purple-500">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400">نسبة العمولة</p>
                <p class="text-xl font-black text-purple-500 font-mono">{{ number_format($user->commission_rate ?? 0, 1) }}%</p>
                <p class="text-[11px] text-gray-400">على المبيعات أو الصيانة</p>
            </div>

            <!-- Pending Advances -->
            <div class="glass-panel p-4 space-y-1 border-r-4 border-amber-500">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي السلف المعلقة</p>
                <p class="text-xl font-black text-amber-500 font-mono">{{ number_format($user->pendingAdvancesTotal(), 2) }} <span class="text-xs">ج.م</span></p>
                <p class="text-[11px] text-gray-400">ستُخصم من راتب الشهر القادم</p>
            </div>

        </div>

        <!-- Personal & Job Information Details -->
        <div class="glass-panel p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-white/5">
                <i class="fa-solid fa-address-card text-[#D41414]"></i>
                <span>البيانات الشخصية والتعاقدية للموظف:</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">رقم الهاتف الأساسي:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white text-sm">{{ $user->phone ?? '—' }}</span>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">هاتف الطوارئ / قريب:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white text-sm">{{ $user->emergency_phone ?? '—' }}</span>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">الرقم القومي:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white text-sm">{{ $user->national_id ?? '—' }}</span>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">البريد الإلكتروني:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $user->email ?? '—' }}</span>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">تاريخ التعيين:</span>
                    <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $user->hire_date ? $user->hire_date->format('Y-m-d') : '—' }}</span>
                </div>

                <div class="p-3 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-200 dark:border-white/5">
                    <span class="text-gray-500 block mb-0.5">نوع احتساب الراتب:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ match($user->salary_type) { 'daily' => 'راتب يومي', 'commission_only' => 'عمولة فقط', default => 'راتب شهري ثابت' } }}</span>
                </div>
            </div>
        </div>

        <!-- Payroll History Table -->
        <div class="glass-panel p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-white/5">
                <i class="fa-solid fa-clock-rotate-left text-[#D41414]"></i>
                <span>سجل الرواتب الشهرية المصروفة للموظف:</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 pb-2">
                            <th class="py-2.5 pr-2">الشهر / السنة</th>
                            <th class="py-2.5 text-center">الأساسي</th>
                            <th class="py-2.5 text-center">مكافآت وعمولات</th>
                            <th class="py-2.5 text-center">سلف وخصومات</th>
                            <th class="py-2.5 text-center">صافي الراتب</th>
                            <th class="py-2.5 text-center">الحالة</th>
                            <th class="py-2.5 text-center">تاريخ الصرف</th>
                            <th class="py-2.5 text-center w-20">قسيمة الراتب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($user->payrolls()->latest('year')->latest('month')->get() as $pr)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                <td class="py-3 pr-2 font-bold text-gray-900 dark:text-white">
                                    {{ $pr->month_name }} {{ $pr->year }}
                                </td>
                                <td class="py-3 text-center font-mono font-bold">{{ number_format($pr->basic_salary, 2) }}</td>
                                <td class="py-3 text-center font-mono text-emerald-600 font-bold">+{{ number_format($pr->total_allowances + $pr->total_bonuses + $pr->total_commissions, 2) }}</td>
                                <td class="py-3 text-center font-mono text-rose-600 font-bold">-{{ number_format($pr->total_advances + $pr->total_deductions, 2) }}</td>
                                <td class="py-3 text-center font-mono font-black text-sm text-gray-900 dark:text-white">{{ number_format($pr->net_salary, 2) }} ج.م</td>
                                <td class="py-3 text-center">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $pr->status_badge_class }}">
                                        {{ $pr->status_name }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-mono text-gray-500">
                                    {{ $pr->paid_at ? $pr->paid_at->format('Y-m-d') : '—' }}
                                </td>
                                <td class="py-3 text-center">
                                    <a href="{{ route('payroll.payslip', $pr->id) }}" target="_blank" class="px-2.5 py-1 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-lg text-gray-700 dark:text-gray-300 font-bold text-[11px] transition">
                                        <i class="fa-solid fa-print"></i> طباعة
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-400 text-xs">
                                    لا توجد رواتب مسجلة لهذا الموظف حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
