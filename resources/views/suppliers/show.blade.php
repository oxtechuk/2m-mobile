<x-app-layout>
    <div class="space-y-6" x-data="{ payModalOpen: false }">

        <!-- Top Header Panel -->
        <div class="glass-panel p-4 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-extrabold text-2xl shadow-inner">
                    {{ mb_substr($supplier->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-white">{{ $supplier->name }}</h1>
                        @if($supplier->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">🟢 نشط</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">🔴 متوقف</span>
                        @endif
                    </div>
                    @if($supplier->company_name)
                        <p class="text-xs text-gray-400 mt-0.5"><i class="fa-solid fa-building text-gray-500 ml-1"></i>{{ $supplier->company_name }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-gray-400">
                        <span><i class="fa-solid fa-phone text-gray-500 ml-1 font-mono"></i>{{ $supplier->phone ?? 'بدون هاتف' }}</span>
                        @if($supplier->email)
                            <span><i class="fa-solid fa-envelope text-gray-500 ml-1 font-mono"></i>{{ $supplier->email }}</span>
                        @endif
                        @if($supplier->address)
                            <span><i class="fa-solid fa-location-dot text-gray-500 ml-1"></i>{{ $supplier->address }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button 
                    @click="payModalOpen = true" 
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg"
                >
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>سداد دفعة للمورد</span>
                </button>

                <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>فاتورة توريد للمورد</span>
                </a>

                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs transition" title="تعديل">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Current Due Balance -->
            <div class="glass-panel p-5 border-r-4 {{ $supplier->current_balance > 0 ? 'border-r-rose-500' : 'border-r-emerald-500' }}">
                <span class="text-xs text-gray-400 block">الرصيد المستحق الحالي للمورد</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black font-mono {{ $supplier->current_balance > 0 ? 'text-rose-400' : ($supplier->current_balance < 0 ? 'text-amber-400' : 'text-emerald-400') }}">
                        {{ number_format(abs($supplier->current_balance), 2) }} ج.م
                    </span>
                    <span class="text-xs font-bold">
                        @if($supplier->current_balance > 0)
                            (مستحق للمورد - دين)
                        @elseif($supplier->current_balance < 0)
                            (رصيد دائن للمحل)
                        @else
                            (خالي من الديون)
                        @endif
                    </span>
                </div>
            </div>

            <!-- Opening Balance -->
            <div class="glass-panel p-5">
                <span class="text-xs text-gray-400 block">الرصيد الافتتاحي</span>
                <span class="text-xl font-bold font-mono text-white mt-2 block">
                    {{ number_format($supplier->opening_balance, 2) }} ج.m
                </span>
                <span class="text-[10px] text-gray-500 mt-1 block">رصيد البداية عند إنشاء الحساب</span>
            </div>

            <!-- Invoices Count -->
            <div class="glass-panel p-5">
                <span class="text-xs text-gray-400 block">إجمالي الفواتير المسجلة</span>
                <span class="text-xl font-bold font-mono text-indigo-400 mt-2 block">
                    {{ $supplier->purchases->count() }} فاتورة توريد
                </span>
                <span class="text-[10px] text-gray-500 mt-1 block">إجمالي المسحوبات والتوريدات</span>
            </div>
        </div>

        <!-- Transactions Ledger Table (كشف الحساب المالية) -->
        <div class="glass-panel p-4 md:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-white/5 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-indigo-400"></i>
                    <span>سجل وحركات كشف الحساب المالي (Statement Ledger)</span>
                </h3>

                <!-- Date Period Pills -->
                <div class="flex items-center gap-1.5 text-xs">
                    <a href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'period' => 'today']) }}" 
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $period === 'today' ? 'bg-indigo-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        اليوم
                    </a>
                    <a href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'period' => 'this_week']) }}" 
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $period === 'this_week' ? 'bg-indigo-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        هذا الأسبوع
                    </a>
                    <a href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'period' => 'this_month']) }}" 
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $period === 'this_month' ? 'bg-indigo-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        هذا الشهر
                    </a>
                    <a href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'period' => 'all']) }}" 
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $period === 'all' ? 'bg-indigo-600 text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        الكل
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                        <tr>
                            <th class="p-3">التاريخ والوقت</th>
                            <th class="p-3">نوع الحركة</th>
                            <th class="p-3">البيان / الملاحظات</th>
                            <th class="p-3">قيمة العملية</th>
                            <th class="p-3">الرصيد بعد الحركة</th>
                            <th class="p-3">طريقة الدفع / المرجع</th>
                            <th class="p-3">المسؤول</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="p-3 text-gray-400 font-mono text-[11px]">
                                    {{ $tx->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td class="p-3">
                                    @if($tx->type === 'purchase_invoice')
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            📦 فاتورة توريد (+)
                                        </span>
                                    @elseif($tx->type === 'payment_out')
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            💵 سداد دفعة (-)
                                        </span>
                                    @elseif($tx->type === 'opening_balance')
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            ⚖️ رصيد افتتاحي
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            🔄 {{ $tx->type }}
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3 text-white">
                                    {{ $tx->notes ?? '-' }}
                                </td>

                                <td class="p-3 font-mono font-bold text-sm">
                                    @if($tx->type === 'purchase_invoice')
                                        <span class="text-rose-400">+{{ number_format($tx->amount, 2) }} ج.م</span>
                                    @elseif($tx->type === 'payment_out')
                                        <span class="text-emerald-400">-{{ number_format($tx->amount, 2) }} ج.م</span>
                                    @else
                                        <span class="text-white">{{ number_format($tx->amount, 2) }} ج.م</span>
                                    @endif
                                </td>

                                <td class="p-3 font-mono font-bold text-gray-300">
                                    {{ number_format($tx->balance_after, 2) }} ج.م
                                </td>

                                <td class="p-3 text-gray-400 text-[11px]">
                                    {{ $tx->payment_method ?? 'نقدي/خزينة' }}
                                </td>

                                <td class="p-3 text-gray-500 text-[11px]">
                                    {{ $tx->creator->name ?? 'النظام' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    لا توجد حركات مالية مسجلة لهذا المورد بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Supplier Products & Recent Purchases Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Recent Purchases -->
            <div class="glass-panel p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                    <h3 class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-indigo-400"></i>
                        <span>آخر فواتير التوريد للمورد</span>
                    </h3>
                </div>

                <div class="space-y-2">
                    @forelse($supplier->purchases->take(5) as $purchase)
                        <div class="p-3 bg-[#0a0a0a] border border-white/5 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="font-bold text-white hover:text-indigo-400 transition font-mono">
                                    {{ $purchase->invoice_number }}
                                </a>
                                <span class="text-[10px] text-gray-500 block mt-0.5">{{ $purchase->purchase_date->format('Y-m-d') }} - {{ $purchase->branch->name ?? '' }}</span>
                            </div>

                            <div class="text-left font-mono">
                                <span class="text-white font-bold block">{{ number_format($purchase->total_amount, 2) }} ج.م</span>
                                <span class="text-[9px] text-gray-400">مدفوع: {{ number_format($purchase->paid_amount, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 py-3 text-center">لا توجد فواتير توريد مسجلة.</p>
                    @endforelse
                </div>
            </div>

            <!-- Linked Products -->
            <div class="glass-panel p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                    <h3 class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-mobile-screen text-amber-400"></i>
                        <span>المنتجات المرتبطة بالمورد كمورد رئيسي</span>
                    </h3>
                </div>

                <div class="space-y-2">
                    @forelse($supplier->products->take(5) as $prod)
                        <div class="p-3 bg-[#0a0a0a] border border-white/5 rounded-xl flex items-center justify-between text-xs">
                            <span class="font-bold text-white">{{ $prod->name }}</span>
                            <span class="text-gray-400 font-mono text-[11px]">تكلفة: {{ number_format($prod->cost_price, 2) }} ج.م</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 py-3 text-center">لم يتم تحديد منتجات مرتبطة بهذا المورد كمورد رئيسي.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Payment Modal (سداد دفعة للمورد) -->
        <div 
            x-show="payModalOpen" 
            x-transition:opacity 
            class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="glass-panel max-w-md w-full p-6 space-y-4 bg-[#121212] border border-white/10 rounded-2xl shadow-2xl" @click.stop>
                <div class="flex items-center justify-between border-b border-white/5 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i>
                        <span>سداد دفعة مالية للمورد {{ $supplier->name }}</span>
                    </h3>
                    <button @click="payModalOpen = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('suppliers.pay', $supplier->id) }}" class="space-y-4">
                    @csrf

                    <!-- Amount -->
                    <div class="space-y-1">
                        <label for="pay_amount" class="block text-xs font-semibold text-gray-300">المبلغ المدفوع (ج.م) <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="amount" 
                            id="pay_amount" 
                            required 
                            max="{{ max(0.01, $supplier->current_balance) }}"
                            value="{{ old('amount', max(0, $supplier->current_balance)) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-emerald-400 font-mono text-left font-bold text-sm focus:outline-none focus:border-emerald-500"
                        >
                        <span class="text-[10px] text-gray-400">الرصيد المستحق للمورد حالياً: {{ number_format($supplier->current_balance, 2) }} ج.م</span>
                    </div>

                    <!-- Wallet Selection -->
                    <div class="space-y-1">
                        <label for="wallet_id" class="block text-xs font-semibold text-gray-300">خصم المبلغ من الخزينة / المحفظة <span class="text-rose-500">*</span></label>
                        <select 
                            name="wallet_id" 
                            id="wallet_id" 
                            required 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-500"
                        >
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">
                                    {{ $w->name }} (الرصيد المتاح: {{ number_format($w->balance, 2) }} ج.م)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div class="space-y-1">
                        <label for="payment_method" class="block text-xs font-semibold text-gray-300">طريقة السداد</label>
                        <select 
                            name="payment_method" 
                            id="payment_method" 
                            required 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-500"
                        >
                            <option value="cash">نقداً (كاش)</option>
                            <option value="bank_transfer">تحويل بنكي / Vodafone Cash</option>
                            <option value="cheque">شيك مصرفي</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label for="pay_notes" class="block text-xs font-semibold text-gray-300">ملاحظات أو رقم الإيصال</label>
                        <input 
                            type="text" 
                            name="notes" 
                            id="pay_notes" 
                            placeholder="مثال: سداد دفعة عن شحنة الاكسسوارات الأخيرة"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-500"
                        >
                    </div>

                    <div class="pt-3 border-t border-white/5 flex justify-end gap-2">
                        <button type="button" @click="payModalOpen = false" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                            إلغاء
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-lg">
                            تأكيد وتسجيل السداد <i class="fa-solid fa-check mr-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
