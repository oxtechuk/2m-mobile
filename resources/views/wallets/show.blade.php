<x-app-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="glass-panel p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">كشف حركة الخزينة/المحفظة</span>
                <h2 class="text-lg font-bold text-white">{{ $wallet->name }}</h2>
            </div>
            
            <div class="text-right">
                <span class="text-[10px] text-gray-500 block">الرصيد الحالي</span>
                <span class="text-2xl font-black text-emerald-400 font-mono">{{ number_format($wallet->balance, 2) }} <span class="text-xs font-normal text-gray-400">{{ setting('default_currency', 'ج.م') }}</span></span>
            </div>
        </div>

        <!-- Transactions Ledger Table -->
        <div class="glass-panel p-4 md:p-6 space-y-4">
            <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">سجل الإيداعات والمسحوبات الأخيرة</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">رقم الحركة</th>
                            <th class="pb-2">التاريخ والوقت</th>
                            <th class="pb-2">البيان / الوصف</th>
                            <th class="pb-2">المبلغ</th>
                            <th class="pb-2">نوع الحركة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-mono text-gray-400">#{{ $tx->id }}</td>
                                <td class="py-3 font-mono">{{ $tx->created_at->format('Y-m-d h:i A') }}</td>
                                <td class="py-3 text-white">{{ $tx->description }}</td>
                                <td class="py-3 font-bold font-mono {{ $tx->type === 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }} {{ number_format($tx->amount, 2) }} {{ setting('default_currency', 'ج.م') }}
                                </td>
                                <td class="py-3">
                                    @if($tx->type === 'credit')
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">إيداع</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">سحب</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">لا توجد حركات مالية مسجلة لهذه الخزينة بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="pt-4 border-t border-white/5 flex justify-end">
                <a href="{{ route('wallets.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    العودة للمحافظ
                </a>
            </div>
        </div>

    </div>
</x-app-layout>