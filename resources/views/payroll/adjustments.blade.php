<x-app-layout>
    <div class="space-y-5" x-data="adjustmentsApp()">
        
        <!-- Header & Quick Actions -->
        <div class="glass-panel p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-amber-500 text-xl font-bold">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">سجل سلف وخصومات ومكافآت الموظفين</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تسجيل السلف النقدية، الجزاءات، الخصومات، والمكافآت لتسويتها وخصمها تلقائياً من راتب الشهر.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Back to Payroll -->
                <a 
                    href="{{ route('payroll.index') }}" 
                    class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 text-xs font-bold transition flex items-center gap-2 border border-gray-300 dark:border-white/10"
                >
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>العودة لمسير الرواتب</span>
                </a>

                <!-- Add New Adjustment Button -->
                <button 
                    type="button" 
                    @click="openCreateModal = true"
                    class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-zinc-950 text-xs font-black transition flex items-center gap-2 shadow-lg"
                >
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>تسجيل سلفة / خصم / مكافأة جديدة</span>
                </button>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- Pending Advances -->
            <div class="glass-panel p-4 flex items-center justify-between border-r-4 border-amber-500">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي السلف المعلقة (قيد الخصم)</p>
                    <p class="text-xl font-black text-amber-500 font-mono">{{ number_format($pendingAdvances, 2) }} <span class="text-xs">ج.م</span></p>
                    <p class="text-[11px] text-gray-400">ستُخصم في مسير الرواتب القادم</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-lg">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>

            <!-- Pending Deductions -->
            <div class="glass-panel p-4 flex items-center justify-between border-r-4 border-rose-500">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي الخصومات والجزاءات المعلقة</p>
                    <p class="text-xl font-black text-rose-500 font-mono">{{ number_format($pendingDeductions, 2) }} <span class="text-xs">ج.م</span></p>
                    <p class="text-[11px] text-gray-400">غياب وتأخيرات وجزاءات قيد التسوية</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-lg">
                    <i class="fa-solid fa-circle-minus"></i>
                </div>
            </div>

            <!-- Pending Bonuses & Allowances -->
            <div class="glass-panel p-4 flex items-center justify-between border-r-4 border-emerald-500">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي المكافآت والحوافز المعلقة</p>
                    <p class="text-xl font-black text-emerald-500 font-mono">{{ number_format($pendingBonuses, 2) }} <span class="text-xs">ج.م</span></p>
                    <p class="text-[11px] text-gray-400">حوافز مضافة للراتب القادم</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-lg">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>

        </div>

        <!-- Filter Bar -->
        <div class="glass-panel p-3.5">
            <form method="GET" action="{{ route('payroll.adjustments') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
                
                <!-- Employee Filter -->
                <div class="sm:col-span-4">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">الموظف:</label>
                    <select name="user_id" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white" onchange="this.form.submit()">
                        <option value="">-- جميع الموظفين --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $userId == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->branch->name ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="sm:col-span-3">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">نوع الحركة:</label>
                    <select name="type" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold" onchange="this.form.submit()">
                        <option value="">-- كل الأنواع --</option>
                        <option value="advance" {{ $type === 'advance' ? 'selected' : '' }}>سلفة نقدية</option>
                        <option value="deduction" {{ $type === 'deduction' ? 'selected' : '' }}>خصم / جزاء</option>
                        <option value="bonus" {{ $type === 'bonus' ? 'selected' : '' }}>مكافأة / حافز</option>
                        <option value="allowance" {{ $type === 'allowance' ? 'selected' : '' }}>بدل إضافي</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-3">
                    <label class="block text-gray-500 dark:text-gray-400 font-bold mb-1">الحالة:</label>
                    <select name="status" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold" onchange="this.form.submit()">
                        <option value="">-- كل الحالات --</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>معلقة (قيد الخصم)</option>
                        <option value="settled" {{ $status === 'settled' ? 'selected' : '' }}>مسواة (تم خصمها من الراتب)</option>
                    </select>
                </div>

                <!-- Reset -->
                <div class="sm:col-span-2 flex items-center gap-2 pt-5">
                    <a href="{{ route('payroll.adjustments') }}" class="w-full text-center py-2 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-white transition font-bold">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <!-- Adjustments Table -->
        <div class="glass-panel p-4 space-y-3">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 pb-2">
                            <th class="py-2.5 pr-3">الموظف</th>
                            <th class="py-2.5 text-center">النوع</th>
                            <th class="py-2.5 text-center">المبلغ</th>
                            <th class="py-2.5 text-center">التاريخ</th>
                            <th class="py-2.5">السبب / البيان</th>
                            <th class="py-2.5 text-center">الحالة</th>
                            <th class="py-2.5 text-center">المسؤول</th>
                            <th class="py-2.5 text-center w-16">إلغاء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($adjustments as $adj)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                
                                <!-- Employee -->
                                <td class="py-3 pr-3">
                                    <span class="font-bold text-gray-900 dark:text-white block">{{ $adj->user->name ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $adj->branch->name ?? ($adj->user->branch->name ?? 'الفرع الرئيسي') }}</span>
                                </td>

                                <!-- Type -->
                                <td class="py-3 text-center">
                                    <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border {{ $adj->type_badge_class }}">
                                        {{ $adj->type_name }}
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="py-3 text-center font-mono font-black text-sm" :class="'{{ $adj->type }}' === 'bonus' || '{{ $adj->type }}' === 'allowance' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                    {{ number_format($adj->amount, 2) }} ج.م
                                </td>

                                <!-- Date -->
                                <td class="py-3 text-center font-mono text-gray-600 dark:text-gray-300">
                                    {{ $adj->date ? $adj->date->format('Y-m-d') : '—' }}
                                </td>

                                <!-- Reason -->
                                <td class="py-3 text-gray-700 dark:text-gray-300 max-w-xs">
                                    <p class="truncate" title="{{ $adj->reason }}">{{ $adj->reason }}</p>
                                    @if($adj->wallet)
                                        <span class="text-[10px] text-gray-400 block font-mono">سُحبت من: {{ $adj->wallet->name }}</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-3 text-center">
                                    @if($adj->status === 'pending')
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 border border-amber-500/20 font-bold">
                                            معلقة (قيد الخصم)
                                        </span>
                                    @elseif($adj->status === 'settled')
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-bold">
                                            تمت التسوية بالراتب
                                        </span>
                                    @else
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-500/10 text-gray-400 font-bold">
                                            ملغاة
                                        </span>
                                    @endif
                                </td>

                                <!-- Created By -->
                                <td class="py-3 text-center text-gray-500 text-[11px]">
                                    {{ $adj->creator->name ?? 'النظام' }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3 text-center">
                                    @if($adj->status === 'pending')
                                        <form action="{{ route('payroll.adjustments.destroy', $adj->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء وحذف هذه الحركة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-500/10 transition" title="إلغاء وحذف">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-400 text-xs">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 text-gray-300 dark:text-zinc-700 block"></i>
                                    لا توجد حركات سلف أو خصومات مسجلة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $adjustments->links() }}
            </div>
        </div>

        <!-- CREATE ADJUSTMENT MODAL -->
        <div 
            x-show="openCreateModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="glass-panel p-5 w-full max-w-lg space-y-4 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-white/10 rounded-2xl shadow-2xl" @click.outside="openCreateModal = false">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-amber-500"></i>
                        <span>تسجيل سلفة أو خصم أو مكافأة لموظف</span>
                    </h3>
                    <button type="button" @click="openCreateModal = false" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form action="{{ route('payroll.adjustments.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <!-- Employee -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">اختر الموظف: <span class="text-rose-500">*</span></label>
                        <select name="user_id" required class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold">
                            <option value="">-- اختر الموظف --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} (الراتب: {{ number_format($emp->salary, 2) }} ج.م - {{ $emp->branch->name ?? '—' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Selector -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">نوع الحركة المالية: <span class="text-rose-500">*</span></label>
                        <select name="type" x-model="selectedType" required class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold">
                            <option value="advance">🏷️ سلفة نقدية (تُسحب فوراً وتُخصم من الراتب القادم)</option>
                            <option value="deduction">🔻 خصم / جزاء / غياب (يُخصم من الراتب)</option>
                            <option value="bonus">⭐ مكافأة / حافز إضافي (تُضاف للراتب)</option>
                            <option value="allowance">💼 بدل إضافي (مواصلات / سكن)</option>
                        </select>
                    </div>

                    <!-- Amount & Date Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">المبلغ (ج.م): <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.5" min="1" name="amount" required placeholder="500.00" class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs font-mono font-bold text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">تاريخ الحركة: <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Wallet (Shown only if Advance is selected) -->
                    <div x-show="selectedType === 'advance'" class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl space-y-2">
                        <label class="block font-bold text-amber-700 dark:text-amber-300">اختر الخزينة لسحب مبلغ السلفة منها الآن:</label>
                        <select name="wallet_id" class="w-full bg-white dark:bg-[#0a0a0a] border border-amber-500/30 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-bold">
                            <option value="">-- بدون سحب فوري من الخزينة --</option>
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-amber-600 dark:text-amber-400">إذا اخترت خزينة، سيتم خصم مبلغ السلفة من رصيدها فوراً وتسجيل حركة سحب.</p>
                    </div>

                    <!-- Reason / Notes -->
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">سبب السلفة / الخصم / المكافأة: <span class="text-rose-500">*</span></label>
                        <textarea name="reason" rows="2" required placeholder="اكتب البيان والسبب بالتفصيل..." class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl p-2.5 text-xs text-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-700 dark:text-gray-300 font-bold">إلغاء</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-zinc-950 font-black shadow-lg">حفظ الحركة المالية</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function adjustmentsApp() {
            return {
                openCreateModal: false,
                selectedType: 'advance'
            };
        }
    </script>
</x-app-layout>
