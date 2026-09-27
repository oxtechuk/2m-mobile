<x-app-layout>
    <div class="space-y-6">

        <!-- Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-indigo-400"></i>
                    <span>سجل فواتير الشراء والتوريد</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">عرض وتتبع كافة الشحنات الموردة ورصيد المخزون الوارد وحالة السداد.</p>
            </div>

            <a href="{{ route('purchases.create') }}" class="px-4 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg glow-primary">
                <i class="fa-solid fa-cart-plus"></i>
                <span>فاتورة توريد جديدة</span>
            </a>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Purchases -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">عدد فواتير الشراء</span>
                    <span class="text-xl font-black text-white font-mono mt-1 block">{{ number_format($stats['total_purchases']) }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي التوريدات الشاملة</span>
                    <span class="text-xl font-black text-indigo-400 font-mono mt-1 block">{{ number_format($stats['total_amount'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">إجمالي المبالغ المسددة</span>
                    <span class="text-xl font-black text-emerald-400 font-mono mt-1 block">{{ number_format($stats['total_paid'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <!-- Total Remaining / Due -->
            <div class="glass-panel p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold">المتبقي (آجل للموردين)</span>
                    <span class="text-xl font-black text-rose-400 font-mono mt-1 block">{{ number_format($stats['total_due'], 2) }} ج.م</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500">
                    <i class="fa-solid fa-clock font-mono"></i>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="glass-panel p-4">
            <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="رقم الفاتورة (مثال: PUR-...)"
                        class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                    >
                </div>

                <div>
                    <select name="supplier_id" class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="">جميع الموردين</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} {{ $sup->company_name ? "({$sup->company_name})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="branch_id" class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="">جميع الفروع</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                        تصفية النتائج <i class="fa-solid fa-magnifying-glass mr-1"></i>
                    </button>
                    @if(request()->hasAny(['search', 'supplier_id', 'branch_id']))
                        <a href="{{ route('purchases.index') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                            إلغاء <i class="fa-solid fa-xmark mr-1"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Purchases Table -->
        <div class="glass-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                        <tr>
                            <th class="p-4">رقم الفاتورة</th>
                            <th class="p-4">المورد</th>
                            <th class="p-4">الفرع المستلم</th>
                            <th class="p-4">التاريخ والوقت</th>
                            <th class="p-4">إجمالي الفاتورة</th>
                            <th class="p-4">المدفوع / المتبقي</th>
                            <th class="p-4">حالة الدفع</th>
                            <th class="p-4 text-center">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($purchases as $purchase)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-4">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="font-mono font-bold text-indigo-400 hover:underline">
                                        {{ $purchase->invoice_number }}
                                    </a>
                                </td>

                                <td class="p-4 font-bold text-white">
                                    <a href="{{ route('suppliers.show', $purchase->supplier_id) }}" class="hover:text-indigo-400 transition">
                                        {{ $purchase->supplier->name ?? 'غير معروف' }}
                                    </a>
                                </td>

                                <td class="p-4 text-gray-300">
                                    {{ $purchase->branch->name ?? '-' }}
                                </td>

                                <td class="p-4 text-gray-400 font-mono text-[11px]">
                                    {{ $purchase->purchase_date->format('Y-m-d H:i') }}
                                </td>

                                <td class="p-4 font-mono font-bold text-white text-sm">
                                    {{ number_format($purchase->total_amount, 2) }} ج.م
                                </td>

                                <td class="p-4 font-mono">
                                    <div class="text-emerald-400 font-bold">مدفوع: {{ number_format($purchase->paid_amount, 2) }}</div>
                                    @if($purchase->due_amount > 0)
                                        <div class="text-rose-400 text-[11px]">متبقي: {{ number_format($purchase->due_amount, 2) }}</div>
                                    @endif
                                </td>

                                <td class="p-4">
                                    @if($purchase->payment_status === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            🟢 مدفوع بالكامل
                                        </span>
                                    @elseif($purchase->payment_status === 'partial')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            🟡 سداد جزئي
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            🔴 آجل / غير مدفوع
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4 text-center">
                                    <a 
                                        href="{{ route('purchases.show', $purchase->id) }}" 
                                        class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg text-xs font-bold transition inline-flex items-center gap-1"
                                    >
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>عرض</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-500">
                                    <i class="fa-solid fa-boxes-packing text-3xl mb-2 block"></i>
                                    <span>لا توجد فواتير توريد مشتريات مسجلة حتى الآن.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="p-4 border-t border-white/5 bg-[#0a0a0a]">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
