<x-app-layout>
    <div class="space-y-5" x-data="payrollIndexApp()">
        
        <!-- Header & Quick Action Bar -->
        <div class="glass-panel p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-[#D41414]/15 border border-[#D41414]/25 flex items-center justify-center text-[#D41414] text-xl font-bold">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">مسير رواتب الموظفين الشهري</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">إدارة واحتساب الرواتب الأساسية، البدلات، العمولات، وتسوية السلف والخصومات وصرفها من الخزائن.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Go to Adjustments & Advances -->
                <a 
                    href="{{ route('payroll.adjustments') }}" 
                    class="px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white text-xs font-bold transition flex items-center gap-2"
                >
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>سجل السلف والخصومات</span>
                </a>

                <!-- Recalculate / Generate Payroll Button -->
                <form action="{{ route('payroll.generate') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    <button 
                        type="submit" 
                        class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-sm"
                        onclick="return confirm('هل تريد احتساب وتحديث مسير الرواتب لجميع الموظفين لشهر {{ $month }}/{{ $year }}؟');"
                    >
                        <i class="fa-solid fa-calculator"></i>
                        <span>احتساب مسير الشهر</span>
                    </button>
                </form>

                <!-- Bulk Disburse Modal Trigger -->
                @if($payrolls->where('status', '!=', 'paid')->count() > 0)
                <button 
                    type="button" 
                    @click="openBulkPayModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition flex items-center gap-2 shadow-lg"
                >
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>صرف الرواتب المحددة</span>
                </button>
                @endif
            </div>
        </div>

        <!-- Filter Bar: Month, Year, Branch -->
        <div class="glass-panel p-3.5">
            <form method="GET" action="{{ route('payroll.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
                
                <!-- Month Selector -->
                <div class="sm:col-span-3">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">الشهر:</label>
                    <select name="month" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold" onchange="this.form.submit()">
                        @foreach([
                            1 => 'يناير (01)', 2 => 'فبراير (02)', 3 => 'مارس (03)', 4 => 'أبريل (04)',
                            5 => 'مايو (05)', 6 => 'يونيو (06)', 7 => 'يوليو (07)', 8 => 'أغسطس (08)',
                            9 => 'سبتمبر (09)', 10 => 'أكتوبر (10)', 11 => 'نوفمبر (11)', 12 => 'ديسمبر (12)'
                        ] as $mNum => $mName)
                            <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Selector -->
                <div class="sm:col-span-2">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">السنة:</label>
                    <select name="year" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold" onchange="this.form.submit()">
                        @for($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Branch Selector -->
                <div class="sm:col-span-4">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">الفرع:</label>
                    <select name="branch_id" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white" onchange="this.form.submit()">
                        <option value="">-- جميع الفروع --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="sm:col-span-3 flex items-center gap-2 pt-5">
                    <button type="submit" class="w-full py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i>
                        <span>تطبيق الفلتر</span>
                    </button>
                    @if($branchId)
                        <a href="{{ route('payroll.index', ['year' => $year, 'month' => $month]) }}" class="px-3 py-2 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-white transition">
                            إلغاء
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">
            
            <!-- 1. Total Basic Salaries -->
            <div class="glass-panel p-3.5 space-y-1">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold">الرواتب الأساسية</p>
                <p class="text-sm sm:text-base font-black text-gray-900 dark:text-white font-mono">{{ number_format($stats['total_basic'], 2) }} <span class="text-[10px] text-gray-400">ج.م</span></p>
                <p class="text-[10px] text-gray-400">لعدد {{ $stats['total_count'] }} موظف</p>
            </div>

            <!-- 2. Allowances & Bonuses -->
            <div class="glass-panel p-3.5 space-y-1 border-r-2 border-emerald-500">
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">+ البدلات والمكافآت</p>
                <p class="text-sm sm:text-base font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($stats['total_allowances'] + $stats['total_bonuses'], 2) }} <span class="text-[10px]">ج.م</span></p>
                <p class="text-[10px] text-gray-400">مكافآت وحوافز</p>
            </div>

            <!-- 3. Commissions -->
            <div class="glass-panel p-3.5 space-y-1 border-r-2 border-purple-500">
                <p class="text-[11px] text-purple-600 dark:text-purple-400 font-bold">+ عمولات المبيعات/الصيانة</p>
                <p class="text-sm sm:text-base font-black text-purple-600 dark:text-purple-400 font-mono">{{ number_format($stats['total_commissions'], 2) }} <span class="text-[10px]">ج.م</span></p>
                <p class="text-[10px] text-gray-400">معتمدة للشهر</p>
            </div>

            <!-- 4. Deductions -->
            <div class="glass-panel p-3.5 space-y-1 border-r-2 border-rose-500">
                <p class="text-[11px] text-rose-600 dark:text-rose-400 font-bold">- الخصومات والجزاءات</p>
                <p class="text-sm sm:text-base font-black text-rose-600 dark:text-rose-400 font-mono">{{ number_format($stats['total_deductions'], 2) }} <span class="text-[10px]">ج.م</span></p>
                <p class="text-[10px] text-gray-400">غياب وتأخير وجزاءات</p>
            </div>

            <!-- 5. Advances Deducted -->
            <div class="glass-panel p-3.5 space-y-1 border-r-2 border-amber-500">
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-bold">- السلف المستقطعة</p>
                <p class="text-sm sm:text-base font-black text-amber-600 dark:text-amber-400 font-mono">{{ number_format($stats['total_advances'], 2) }} <span class="text-[10px]">ج.م</span></p>
                <p class="text-[10px] text-gray-400">سلف مسحوبة خلال الشهر</p>
            </div>

            <!-- 6. Total Net Payable -->
            <div class="glass-panel p-3.5 space-y-1 bg-[#D41414]/10 border border-[#D41414]/30">
                <p class="text-[11px] text-[#D41414] font-bold">= صافي الرواتب المستحقة</p>
                <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white font-mono">{{ number_format($stats['total_net'], 2) }} <span class="text-[10px] text-gray-400">ج.م</span></p>
                <p class="text-[10px] text-emerald-500 font-bold">تم صرف {{ $stats['paid_count'] }} من {{ $stats['total_count'] }}</p>
            </div>

        </div>

        <!-- Payroll Records Table -->
        <div class="glass-panel p-4 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-[#D41414]"></i>
                    <span>كشف رواتب الموظفين (شهر {{ $month }}/{{ $year }})</span>
                </h3>
                <span class="text-xs text-gray-500">عدد الموظفين في الكشف: <strong class="text-gray-900 dark:text-white">{{ $payrolls->count() }}</strong></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 pb-2">
                            <th class="py-2.5 pr-3">الموظف والفرع</th>
                            <th class="py-2.5 text-center">الأساسي</th>
                            <th class="py-2.5 text-center">بدلات ومكافآت</th>
                            <th class="py-2.5 text-center">عمولات</th>
                            <th class="py-2.5 text-center">خصومات وجزاءات</th>
                            <th class="py-2.5 text-center">سلف مستقطعة</th>
                            <th class="py-2.5 text-center">صافي الراتب</th>
                            <th class="py-2.5 text-center">الحالة</th>
                            <th class="py-2.5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($payrolls as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                
                                <!-- Employee Info -->
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center font-bold text-gray-700 dark:text-gray-300 shrink-0">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $p->user_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-[#D41414] transition">
                                                {{ $p->user->name ?? '—' }}
                                            </a>
                                            <div class="text-[10px] text-gray-500 flex items-center gap-2 mt-0.5">
                                                <span>{{ $p->branch->name ?? ($p->user->branch->name ?? 'الفرع الرئيسي') }}</span>
                                                <span>•</span>
                                                <span class="font-mono">{{ $p->user->phone ?? '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Basic Salary -->
                                <td class="py-3 text-center font-mono font-bold text-gray-900 dark:text-white">
                                    {{ number_format($p->basic_salary, 2) }}
                                </td>

                                <!-- Allowances & Bonuses -->
                                <td class="py-3 text-center font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                    +{{ number_format($p->total_allowances + $p->total_bonuses, 2) }}
                                </td>

                                <!-- Commissions -->
                                <td class="py-3 text-center font-mono text-purple-600 dark:text-purple-400 font-bold">
                                    +{{ number_format($p->total_commissions, 2) }}
                                </td>

                                <!-- Deductions -->
                                <td class="py-3 text-center font-mono text-rose-600 dark:text-rose-400 font-bold">
                                    -{{ number_format($p->total_deductions, 2) }}
                                </td>

                                <!-- Advances -->
                                <td class="py-3 text-center font-mono text-amber-600 dark:text-amber-400 font-bold">
                                    -{{ number_format($p->total_advances, 2) }}
                                </td>

                                <!-- Net Salary -->
                                <td class="py-3 text-center font-mono font-black text-sm text-gray-900 dark:text-white">
                                    <span class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/10">
                                        {{ number_format($p->net_salary, 2) }} ج.م
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="py-3 text-center">
                                    <span class="text-[10px] px-2.5 py-1 rounded-full font-bold {{ $p->status_badge_class }}">
                                        {{ $p->status_name }}
                                    </span>
                                    @if($p->status === 'paid' && $p->paid_at)
                                        <span class="block text-[9px] text-gray-400 mt-1 font-mono">
                                            {{ $p->paid_at->format('Y-m-d') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        
                                        <!-- Printable Payslip -->
                                        <a 
                                            href="{{ route('payroll.payslip', $p->id) }}" 
                                            target="_blank" 
                                            class="px-2 py-1.5 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 text-[11px] font-bold transition flex items-center gap-1"
                                            title="طباعة قسيمة الراتب"
                                        >
                                            <i class="fa-solid fa-print"></i>
                                            <span>إيصال</span>
                                        </a>

                                        <!-- Approve Button (if draft) -->
                                        @if($p->status === 'draft')
                                            <form action="{{ route('payroll.approve', $p->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button 
                                                    type="submit" 
                                                    class="px-2.5 py-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-600 dark:text-blue-400 hover:text-white text-[11px] font-bold transition"
                                                    title="اعتماد الراتب"
                                                >
                                                    اعتماد
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Pay Single Button (if not paid) -->
                                        @if($p->status !== 'paid')
                                            <button 
                                                type="button" 
                                                @click="openPayModal({{ $p->id }}, '{{ addslashes($p->user->name ?? '') }}', {{ $p->net_salary }})"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black transition shadow-sm flex items-center gap-1"
                                                title="صرف الراتب المالي"
                                            >
                                                <i class="fa-solid fa-money-bill-wave"></i>
                                                <span>صرف</span>
                                            </button>
                                        @else
                                            <span class="text-emerald-500 text-xs font-bold">
                                                <i class="fa-solid fa-circle-check"></i> تم الصرف
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-gray-400 text-xs">
                                    <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-300 dark:text-zinc-700 block"></i>
                                    لم يتم توليد مسير رواتب لهذا الشهر بعد.
                                    <form action="{{ route('payroll.generate') }}" method="POST" class="inline mt-2">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                                        <button type="submit" class="block mx-auto mt-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs transition">
                                            ⚙️ اضغط هنا لاحتساب وتوليد مسير الشهر تلقائياً
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: Single Pay Payroll -->
        <div 
            x-show="payModal.open" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="glass-panel p-5 w-full max-w-md space-y-4 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-white/10 rounded-2xl shadow-2xl" @click.outside="payModal.open = false">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>
                        <span>صرف راتب الموظف: <span class="text-emerald-500" x-text="payModal.employeeName"></span></span>
                    </h3>
                    <button type="button" @click="payModal.open = false" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form :action="'/payroll/' + payModal.payrollId + '/pay'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-500/20 rounded-xl text-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">المبلغ الصافي المطلوب صرفه:</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="payModal.netSalary.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' ج.م'"></span>
                    </div>

                    <!-- Wallet Selector -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">اختر الخزينة / المحفظة للصرف منها: <span class="text-rose-500">*</span></label>
                        <select name="wallet_id" required class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold">
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">ملاحظات إضافية:</label>
                        <textarea name="notes" rows="2" placeholder="ملاحظات الصرف..." class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl p-2.5 text-xs text-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="payModal.open = false" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-700 dark:text-gray-300 font-bold">إلغاء</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-lg">تأكيد الخصم والصرف الآن</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Bulk Pay Payrolls -->
        <div 
            x-show="openBulkPayModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="glass-panel p-5 w-full max-w-lg space-y-4 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-white/10 rounded-2xl shadow-2xl" @click.outside="openBulkPayModal = false">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>
                        <span>صرف رواتب متعددة دفعة واحدة</span>
                    </h3>
                    <button type="button" @click="openBulkPayModal = false" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form action="{{ route('payroll.bulk-pay') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <p class="text-gray-600 dark:text-gray-300">حدد الموظفين المراد صرف رواتبهم من كشف هذا الشهر:</p>

                    <div class="max-h-52 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5 border border-gray-200 dark:border-white/10 rounded-xl p-2 bg-gray-50 dark:bg-black/20">
                        @foreach($payrolls->where('status', '!=', 'paid') as $unpaid)
                            <label class="flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="payroll_ids[]" value="{{ $unpaid->id }}" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $unpaid->user->name ?? '—' }}</span>
                                </div>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($unpaid->net_salary, 2) }} ج.م</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Wallet Selector -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">اختر الخزينة / المحفظة للصرف منها: <span class="text-rose-500">*</span></label>
                        <select name="wallet_id" required class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold">
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="openBulkPayModal = false" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-700 dark:text-gray-300 font-bold">إلغاء</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-lg">تأكيد الصرف الجماعي</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function payrollIndexApp() {
            return {
                openBulkPayModal: false,
                payModal: {
                    open: false,
                    payrollId: null,
                    employeeName: '',
                    netSalary: 0
                },
                openPayModal(id, name, net) {
                    this.payModal.payrollId = id;
                    this.payModal.employeeName = name;
                    this.payModal.netSalary = net;
                    this.payModal.open = true;
                }
            };
        }
    </script>
</x-app-layout>
