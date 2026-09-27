<x-app-layout>
    <div class="space-y-6">

        <!-- Top Header & Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-building-user text-indigo-400"></i>
                    <span>دليل الموردين والحسابات</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">إدارة الشركات والموردين، متابعة الديون والمستحقات المالية وسداد المشتريات.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>فاتورة توريد جديدة</span>
                </a>

                <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg glow-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>إضافة مورد جديد</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Suppliers -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي الموردين</span>
                    <span class="text-xl font-black text-white font-mono mt-1 block">{{ number_format($stats['total_suppliers']) }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>

            <!-- Active Suppliers -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">الموردين النشطين</span>
                    <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">{{ number_format($stats['active_suppliers']) }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <!-- Total Debt / Due to Suppliers -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي الديون المستحقة للموردين</span>
                    <span class="text-xl font-black text-rose-500 font-mono mt-1 block">{{ number_format($stats['total_due'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>

            <!-- Total Credit Balance -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">رصيد دائنية (لصالح المؤسسة)</span>
                    <span class="text-xl font-black text-amber-400 font-mono mt-1 block">{{ number_format($stats['total_credit'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
        </div>

        <!-- Filter Bar & Search -->
        <div class="glass-panel p-4">
            <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="ابحث باسم المورد، اسم الشركة، أو رقم الهاتف..."
                        class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                    >
                </div>

                <div>
                    <select name="status" class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="">جميع الحالات (نشط وغير نشط)</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط فقط</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                        بحث وتصفية <i class="fa-solid fa-magnifying-glass mr-1"></i>
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('suppliers.index') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                            إلغاء <i class="fa-solid fa-xmark mr-1"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Suppliers Table -->
        <div class="glass-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                        <tr>
                            <th class="p-4">المورد / الشركة</th>
                            <th class="p-4">بيانات الاتصال</th>
                            <th class="p-4">فواتير التوريد</th>
                            <th class="p-4">الرصيد الحالي</th>
                            <th class="p-4">الحالة</th>
                            <th class="p-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($suppliers as $supplier)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold shrink-0">
                                            {{ mb_substr($supplier->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="font-bold text-white hover:text-indigo-400 transition block">
                                                {{ $supplier->name }}
                                            </a>
                                            @if($supplier->company_name)
                                                <span class="text-[10px] text-gray-400 block">{{ $supplier->company_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <div class="space-y-0.5">
                                        <div class="text-gray-300 font-mono text-[11px] flex items-center gap-1">
                                            <i class="fa-solid fa-phone text-[10px] text-gray-500"></i>
                                            <span>{{ $supplier->phone ?? 'غير محدد' }}</span>
                                        </div>
                                        @if($supplier->email)
                                            <div class="text-gray-400 text-[10px] font-mono">{{ $supplier->email }}</div>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-gray-300 font-mono font-bold">
                                        {{ $supplier->purchases_count }} فاتورة
                                    </span>
                                </td>

                                <td class="p-4">
                                    @if($supplier->current_balance > 0)
                                        <span class="text-rose-400 font-bold font-mono text-sm block">
                                            {{ number_format($supplier->current_balance, 2) }} ج.م
                                        </span>
                                        <span class="text-[9px] text-rose-500/80 block">مستحق للمورد (دين)</span>
                                    @elseif($supplier->current_balance < 0)
                                        <span class="text-amber-400 font-bold font-mono text-sm block">
                                            {{ number_format(abs($supplier->current_balance), 2) }} ج.م
                                        </span>
                                        <span class="text-[9px] text-amber-500/80 block">رصيد لصالح المحل</span>
                                    @else
                                        <span class="text-emerald-400 font-bold font-mono text-sm block">0.00 ج.م</span>
                                        <span class="text-[9px] text-emerald-500/80 block">خالي من الديون</span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    @if($supplier->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            🟢 نشط
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            🔴 متوقف
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a 
                                            href="{{ route('suppliers.show', $supplier->id) }}" 
                                            class="px-2.5 py-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                            title="كشف الحساب والعمليات"
                                        >
                                            <i class="fa-solid fa-file-invoice"></i>
                                            <span>كشف الحساب</span>
                                        </a>

                                        <a 
                                            href="{{ route('suppliers.edit', $supplier->id) }}" 
                                            class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg text-xs transition"
                                            title="تعديل البيانات"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" onsubmit="return confirm('هل أنت تأكد من إمكانية حذف المورد؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded-lg text-xs transition" title="حذف">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 block"></i>
                                    <span>لا يوجد موردين مسجلين حالياً بالقائمة.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
                <div class="p-4 border-t border-white/5 bg-[#0a0a0a]">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
