<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-rotate-left text-[#D41414]"></i>
                    <span>إدارة مرتجعات المبيعات</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">سجل المرتجعات ومعالجة إرجاع الأصناف مع التعديل التلقائي للمخزون والخزينة</p>
            </div>

            @can('process-return')
            <a href="{{ route('returns.create') }}" class="px-4 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-xl text-xs transition glow-primary flex items-center justify-center gap-2 shadow-lg">
                <i class="fa-solid fa-plus"></i>
                <span>تسجيل مرتجع جديد</span>
            </a>
            @endcan
        </div>

        <!-- Search & Filter Form -->
        <div class="glass-panel p-4 rounded-2xl">
            <form method="GET" action="{{ route('returns.index') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="البحث برقم الفاتورة، اسم العميل أو الهاتف..." 
                        class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
                @if(request('search'))
                <a href="{{ route('returns.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs font-bold rounded-xl transition flex items-center">
                    إلغاء التصفية
                </a>
                @endif
            </form>
        </div>

        <!-- Returns List Table -->
        <div class="glass-panel rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 uppercase">
                        <tr>
                            <th class="px-4 py-3 font-semibold">رقم الفاتورة</th>
                            <th class="px-4 py-3 font-semibold">الفرع</th>
                            <th class="px-4 py-3 font-semibold">العميل</th>
                            <th class="px-4 py-3 font-semibold">المسؤول</th>
                            <th class="px-4 py-3 font-semibold">حالة الفاتورة</th>
                            <th class="px-4 py-3 font-semibold">إجمالي الفاتورة</th>
                            <th class="px-4 py-3 font-semibold">تاريخ الفاتورة</th>
                            <th class="px-4 py-3 font-semibold text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-gray-300">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3 font-mono font-bold text-white">#{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $sale->branch->name ?? 'عام' }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $sale->customer->name ?? 'عميل نقدي' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $sale->user->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($sale->status === 'cancelled')
                                    <span class="px-2 py-0.5 rounded-md bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold">ملغاة بالكامل</span>
                                @elseif($sale->status === 'partially_refunded')
                                    <span class="px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold">مرتجع جزئي</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold">مكتملة</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-emerald-400">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                            <td class="px-4 py-3 text-gray-400 dir-ltr text-right">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-center space-x-2 space-x-reverse">
                                <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white rounded-lg transition inline-block" title="عرض الفاتورة">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('process-return')
                                @if($sale->status !== 'cancelled')
                                <a href="{{ route('returns.create', ['sale_id' => $sale->id]) }}" class="p-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg transition inline-block" title="إجراء مرتجع">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <i class="fa-solid fa-rotate-left text-3xl text-white/10 mb-2"></i>
                                <p>لا توجد سجلات مرتجعات مبيعات حتى الآن.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
            <div class="p-4 border-t border-white/5">
                {{ $sales->links() }}
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
