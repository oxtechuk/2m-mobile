<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/5 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-calculator ml-2 text-[#D41414]"></i>التقارير المالية والتدفقات</h2>
                <p class="text-xs text-gray-500 mt-1">حساب صافي الأرباح ومطابقة إيرادات نقاط البيع مع المصروفات والسيولة.</p>
            </div>
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" class="h-10 w-auto rounded-lg object-contain">
            @endif
        </div>

        <!-- Profit / Loss Summary Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Revenue -->
            <div class="glass-panel p-5 space-y-2">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">إيرادات المبيعات</span>
                <span class="text-2xl font-black text-emerald-400 font-mono">+ {{ number_format($stats['sales_revenue'], 2) }} ج.م</span>
                <p class="text-[9px] text-gray-500">حصر فواتير الكاشير المعتمدة.</p>
            </div>

            <!-- Expenses -->
            <div class="glass-panel p-5 space-y-2">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">إجمالي المصروفات التشغيلية</span>
                <span class="text-2xl font-black text-rose-500 font-mono">- {{ number_format($stats['total_expenses'], 2) }}  ج.م</span>
                <p class="text-[9px] text-gray-500">الرواتب، الإيجارات، والفواتير.</p>
            </div>

            <!-- Net Profit -->
            @php
                $net = $stats['sales_revenue'] - $stats['total_expenses'];
            @endphp
            <div class="glass-panel p-5 space-y-2">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">صافي التدفق المالي</span>
                <span class="text-2xl font-black font-mono {{ $net >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $net >= 0 ? '+' : '' }} {{ number_format($net, 2) }} ج.م
                </span>
                <p class="text-[9px] text-gray-500">الإيرادات مطروحاً منها المصروفات.</p>
            </div>
        </div>

        <!-- Wallet liquidity -->
        <div class="glass-panel p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <h3 class="text-xs font-bold text-white"><i class="fa-solid fa-vault text-[#D41414] ml-1.5"></i>السيولة النقدية المتوفرة في الخزائن</h3>
                <span class="text-xs text-gray-400">الرصيد الفعلي الحالي</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-500 block">إجمالي أرصدة الخزائن والمحافظ</span>
                    <span class="text-2xl font-black text-white font-mono">{{ number_format($stats['wallet_balances'], 2) }} ج.م</span>
                </div>
                
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-coins text-2xl"></i>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>