<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header & Top Navigation Tabs -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-[#D41414]"></i>
                    <span>سجل المعاملات الشامل (المعاملات المالية، المنتجات المباعة، والتحويلات)</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">سجل تفصيلي دقيق لكل حركة مالية، بيع منتج، وتحويل أموال في النظام</p>
            </div>

            <!-- View Mode Switcher Pills -->
            <div class="flex items-center gap-2 bg-[#0a0a0a] p-1.5 rounded-2xl border border-white/10 shrink-0 text-xs">
                <a 
                    href="{{ route('transactions.index', ['view_mode' => 'financial']) }}" 
                    class="px-3.5 py-2 rounded-xl font-bold transition flex items-center gap-1.5"
                    class="{{ $viewMode === 'financial' ? 'bg-[#D41414] text-white shadow-lg glow-primary' : 'text-gray-400 hover:text-white' }}"
                    style="{{ $viewMode === 'financial' ? 'background-color: #D41414; color: white;' : '' }}"
                >
                    <i class="fa-solid fa-wallet text-xs"></i>
                    <span>المعاملات المالية</span>
                </a>

                <a 
                    href="{{ route('transactions.index', ['view_mode' => 'products']) }}" 
                    class="px-3.5 py-2 rounded-xl font-bold transition flex items-center gap-1.5"
                    style="{{ $viewMode === 'products' ? 'background-color: #D41414; color: white;' : '' }}"
                >
                    <i class="fa-solid fa-boxes-stacked text-xs"></i>
                    <span>المنتجات المباعة</span>
                </a>

                <a 
                    href="{{ route('transactions.index', ['view_mode' => 'transfers']) }}" 
                    class="px-3.5 py-2 rounded-xl font-bold transition flex items-center gap-1.5"
                    style="{{ $viewMode === 'transfers' ? 'background-color: #D41414; color: white;' : '' }}"
                >
                    <i class="fa-solid fa-money-bill-transfer text-xs"></i>
                    <span>تحويلات الأموال</span>
                </a>
            </div>
        </div>

        @if($viewMode === 'products')
            <!-- MODE 2: SOLD PRODUCTS LOG -->
            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass-panel p-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">إجمالي الكميات المباعة</span>
                        <h3 class="text-lg font-mono font-black text-emerald-400 mt-0.5">{{ number_format($totalProductsQty) }} قطعة</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-amber-500/20 bg-amber-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">إجمالي قيمة المنتجات المباعة</span>
                        <h3 class="text-lg font-mono font-black text-amber-400 mt-0.5">{{ number_format($totalProductsRevenue, 2) }} {{ setting('default_currency', 'ج.م') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-[#D41414] fa-sack-dollar"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-blue-500/20 bg-blue-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">عدد الأصناف المتنوعة</span>
                        <h3 class="text-lg font-mono font-black text-blue-400 mt-0.5">{{ number_format($uniqueProductsCount) }} صنف</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Form -->
            <div class="glass-panel p-4 rounded-2xl">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                    <input type="hidden" name="view_mode" value="products">

                    <div class="relative sm:col-span-2">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ابحث باسم المنتج، الباركود، الـ IMEI، أو رقم الفاتورة..." 
                            class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                        >
                    </div>

                    <!-- Branch Filter Dropdown -->
                    <select name="branch_id" class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]">
                        <option value="all" {{ ($branchId == 'all' || !request('branch_id')) ? 'selected' : '' }}>جميع الفروع</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (request('branch_id') == $b->id) ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>

                    <input 
                        type="date" 
                        name="from_date" 
                        value="{{ request('from_date') }}"
                        class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]"
                    >
                    <input 
                        type="date" 
                        name="to_date" 
                        value="{{ request('to_date') }}"
                        class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]"
                    >

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold text-xs rounded-xl transition shadow-lg glow-primary flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-filter"></i> تصفية
                        </button>
                        @if(request()->anyFilled(['search', 'branch_id', 'from_date', 'to_date']))
                        <a href="{{ route('transactions.index', ['view_mode' => 'products']) }}" class="p-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs font-bold rounded-xl transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Sold Products Data Table -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 uppercase">
                            <tr>
                                <th class="px-4 py-3 font-semibold">المنتج / الصنف</th>
                                <th class="px-4 py-3 font-semibold text-center">الكمية المباعة</th>
                                <th class="px-4 py-3 font-semibold text-center">سعر القطعة</th>
                                <th class="px-4 py-3 font-semibold text-center">الإجمالي</th>
                                <th class="px-4 py-3 font-semibold">رقم الفاتورة والعميل</th>
                                <th class="px-4 py-3 font-semibold">الفرع والكاشير</th>
                                <th class="px-4 py-3 font-semibold">تاريخ ووقت البيع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-gray-300">
                            @forelse($items as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-white text-xs">{{ $item->product->name ?? 'منتج غير معرف' }}</p>
                                    @if($item->serials && $item->serials->count() > 0)
                                        <p class="text-[10px] text-amber-400 font-mono mt-0.5">IMEI: {{ $item->serials->pluck('serial_number')->join(', ') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-white">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-gray-300">{{ number_format($item->unit_price, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-emerald-400">{{ number_format($item->total, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('sales.show', $item->sale_id) }}" class="font-mono font-bold text-[#D41414] hover:underline block">#{{ $item->sale->invoice_number ?? '-' }}</a>
                                    <span class="text-[10px] text-gray-400">{{ $item->sale->customer->name ?? 'عميل نقدي' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-white block">{{ $item->sale->branch->name ?? 'عام' }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $item->sale->cashier->name ?? $item->sale->user->name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-400 dir-ltr text-right">
                                    <span class="block font-mono text-white text-[11px]">{{ $item->created_at->format('Y-m-d h:i A') }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-boxes-stacked text-3xl text-white/10 mb-2"></i>
                                    <p>لا توجد سجلات مبيعات منتجات مطابقة لخيارات البحث.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $items->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        @elseif($viewMode === 'transfers')
            <!-- MODE 3: MONEY TRANSFERS LOG -->
            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass-panel p-4 rounded-2xl border border-blue-500/20 bg-blue-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">إجمالي المبالغ المحولة</span>
                        <h3 class="text-lg font-mono font-black text-blue-400 mt-0.5">{{ number_format($totalTransferAmount, 2) }} {{ setting('default_currency', 'ج.م') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">التحويلات المعتمدة</span>
                        <h3 class="text-lg font-mono font-black text-emerald-400 mt-0.5">{{ number_format($approvedCount) }} عملية</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-amber-500/20 bg-amber-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">التحويلات المعلقة</span>
                        <h3 class="text-lg font-mono font-black text-amber-400 mt-0.5">{{ number_format($pendingCount) }} عملية</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Form -->
            <div class="glass-panel p-4 rounded-2xl">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <input type="hidden" name="view_mode" value="transfers">

                    <div class="relative sm:col-span-2">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ابحث بملاحظات التحويل أو اسم المحول..." 
                            class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                        >
                    </div>

                    <select name="status" class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]">
                        <option value="">جميع الحالات</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمدة (Approved)</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>معلقة (Pending)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوضة (Rejected)</option>
                    </select>

                    <input 
                        type="date" 
                        name="from_date" 
                        value="{{ request('from_date') }}"
                        class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]"
                    >

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold text-xs rounded-xl transition shadow-lg glow-primary flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-filter"></i> تصفية
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'from_date']))
                        <a href="{{ route('transactions.index', ['view_mode' => 'transfers']) }}" class="p-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs font-bold rounded-xl transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Money Transfers Data Table -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 uppercase">
                            <tr>
                                <th class="px-4 py-3 font-semibold">رقم التحويل</th>
                                <th class="px-4 py-3 font-semibold">من محفظة / فرع</th>
                                <th class="px-4 py-3 font-semibold">إلى محفظة / فرع</th>
                                <th class="px-4 py-3 font-semibold">المبلغ المحول</th>
                                <th class="px-4 py-3 font-semibold">حالة التحويل</th>
                                <th class="px-4 py-3 font-semibold">المحول والموافق</th>
                                <th class="px-4 py-3 font-semibold">تاريخ ووقت التحويل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-gray-300">
                            @forelse($transfers as $trf)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 font-mono font-bold text-white">#TRF-{{ $trf->id }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-white block">{{ $trf->fromWallet->name ?? 'خزينة' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $trf->fromWallet->branch->name ?? 'عام' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-white block">{{ $trf->toWallet->name ?? 'خزينة' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $trf->toWallet->branch->name ?? 'عام' }}</span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-blue-400 text-sm">
                                    {{ number_format($trf->amount, 2) }} {{ setting('default_currency', 'ج.م') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($trf->status === 'approved')
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold">معتمدة</span>
                                    @elseif($trf->status === 'pending')
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold">قيد الانتظار</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold">مرفوضة</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="block text-white font-medium">المحول: {{ $trf->transferredBy->name ?? '-' }}</span>
                                    @if($trf->approvedBy)
                                        <span class="text-[10px] text-gray-400 block">الموافق: {{ $trf->approvedBy->name }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 dir-ltr text-right">
                                    <span class="block font-mono text-white text-[11px]">{{ $trf->created_at->format('Y-m-d h:i A') }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $trf->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-money-bill-transfer text-3xl text-white/10 mb-2"></i>
                                    <p>لا توجد سجلات تحويلات ماليّة مطابقة.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transfers->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        @else
            <!-- MODE 1: FINANCIAL TRANSACTIONS (DEFAULT) -->
            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="glass-panel p-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">إجمالي المقبوضات (إيداع)</span>
                        <h3 class="text-lg font-mono font-black text-emerald-400 mt-0.5">{{ number_format($totalCredits, 2) }} {{ setting('default_currency', 'ج.م') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-arrow-down-left"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-rose-500/20 bg-rose-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">إجمالي المدفوعات (سحب)</span>
                        <h3 class="text-lg font-mono font-black text-rose-400 mt-0.5">{{ number_format($totalDebits, 2) }} {{ setting('default_currency', 'ج.م') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-arrow-up-right"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-amber-500/20 bg-amber-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">صافي التدفق المالي</span>
                        <h3 class="text-lg font-mono font-black {{ $netBalance >= 0 ? 'text-amber-400' : 'text-rose-400' }} mt-0.5">{{ number_format($netBalance, 2) }} {{ setting('default_currency', 'ج.م') }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>

                <div class="glass-panel p-4 rounded-2xl border border-blue-500/20 bg-blue-500/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">عدد المعاملات المسجلة</span>
                        <h3 class="text-lg font-mono font-black text-blue-400 mt-0.5">{{ number_format($totalCount) }} عملية</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Form -->
            <div class="glass-panel p-4 rounded-2xl space-y-3">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <input type="hidden" name="view_mode" value="financial">

                    <div class="relative sm:col-span-2">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ابحث بالبيان، رقم المرجع، أو اسم المستخدم..." 
                            class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414] transition"
                        >
                    </div>

                    <select name="category" class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]">
                        <option value="">جميع الفئات</option>
                        <option value="sale" {{ request('category') === 'sale' ? 'selected' : '' }}>مبيعات (Sale)</option>
                        <option value="refund" {{ request('category') === 'refund' ? 'selected' : '' }}>مرتجع (Refund)</option>
                        <option value="expense" {{ request('category') === 'expense' ? 'selected' : '' }}>مصروفات (Expense)</option>
                        <option value="transfer" {{ request('category') === 'transfer' ? 'selected' : '' }}>تحويل مالي (Transfer)</option>
                        <option value="deposit" {{ request('category') === 'deposit' ? 'selected' : '' }}>إيداع مباشر (Deposit)</option>
                        <option value="withdrawal" {{ request('category') === 'withdrawal' ? 'selected' : '' }}>سحب مباشر (Withdrawal)</option>
                    </select>

                    <select name="type" class="bg-[#0a0a0a] border border-white/10 text-gray-300 text-xs rounded-xl px-3 py-2 focus:border-[#D41414]">
                        <option value="">جميع الأنواع</option>
                        <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>إيداع (Credit +)</option>
                        <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>سحب (Debit -)</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold text-xs rounded-xl transition shadow-lg glow-primary flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-filter"></i> تصفية
                        </button>
                        @if(request()->anyFilled(['search', 'category', 'type', 'from_date', 'to_date']))
                        <a href="{{ route('transactions.index', ['view_mode' => 'financial']) }}" class="p-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs font-bold rounded-xl transition flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Transactions Master Data Table -->
            <div class="glass-panel rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 uppercase">
                            <tr>
                                <th class="px-4 py-3 font-semibold">رقم المعاملة</th>
                                <th class="px-4 py-3 font-semibold">النوع والنوع الفرعي</th>
                                <th class="px-4 py-3 font-semibold">الخزينة والفرع</th>
                                <th class="px-4 py-3 font-semibold">المبلغ</th>
                                <th class="px-4 py-3 font-semibold">الرصيد بعدها</th>
                                <th class="px-4 py-3 font-semibold">البيان والسبب</th>
                                <th class="px-4 py-3 font-semibold">المسؤول</th>
                                <th class="px-4 py-3 font-semibold">التاريخ والوقت</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-gray-300">
                            @forelse($transactions as $trx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 font-mono font-bold text-white">#TRX-{{ $trx->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        @if($trx->type === 'credit')
                                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-down-left text-[10px]"></i> إيداع
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-up-right text-[10px]"></i> سحب
                                            </span>
                                        @endif

                                        <span class="px-2 py-0.5 rounded bg-white/5 text-gray-400 text-[10px] uppercase font-semibold">
                                            {{ $trx->category ?? 'عام' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-white block">{{ $trx->wallet->name ?? 'خزينة' }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $trx->wallet->branch->name ?? 'عام' }}</span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-sm {{ $trx->type === 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $trx->type === 'credit' ? '+' : '-' }}{{ number_format($trx->amount, 2) }} {{ setting('default_currency', 'ج.م') }}
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-300">
                                    {{ number_format($trx->balance_after, 2) }} {{ setting('default_currency', 'ج.م') }}
                                </td>
                                <td class="px-4 py-3 text-gray-300 max-w-xs truncate">
                                    {{ $trx->description }}
                                </td>
                                <td class="px-4 py-3 text-gray-400">
                                    {{ $trx->performedBy->name ?? 'النظام' }}
                                </td>
                                <td class="px-4 py-3 text-gray-400 dir-ltr text-right">
                                    <span class="block font-mono text-white text-[11px]">{{ $trx->created_at->format('Y-m-d h:i A') }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $trx->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-receipt text-3xl text-white/10 mb-2"></i>
                                    <p>لا توجد معاملات ماليّة مسجلة بالمواصفات المحددة.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                <div class="p-4 border-t border-white/5">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
