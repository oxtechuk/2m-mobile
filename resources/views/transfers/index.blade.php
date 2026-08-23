<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: List of Transfers (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-money-bill-transfer ml-2 text-[#D41414]"></i>سجل تحويلات الأموال</h2>
                    <p class="text-xs text-gray-500 mt-1">تتبع عمليات نقل الأرصدة النقدية بين الخزائن والمحافظ الإلكترونية بالفرع.</p>
                </div>
            </div>

            <!-- Transfers Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">رقم المعاملة</th>
                            <th class="pb-2">المرسل من</th>
                            <th class="pb-2">المستقبل في</th>
                            <th class="pb-2">المبلغ المحول</th>
                            <th class="pb-2">التاريخ والوقت</th>
                            <th class="pb-2">الموظف</th>
                            <th class="pb-2">حالة التحويل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transfers as $tx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-mono text-gray-400">#{{ $tx->id }}</td>
                                <td class="py-3 font-semibold text-white">{{ $tx->fromWallet->name }}</td>
                                <td class="py-3 font-semibold text-white">{{ $tx->toWallet->name }}</td>
                                <td class="py-3 font-bold font-mono text-emerald-400">{{ number_format($tx->amount, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="py-3 font-mono text-gray-400">{{ $tx->created_at->format('Y-m-d h:i A') }}</td>
                                <td class="py-3 text-gray-300">{{ $tx->transferredBy->name ?? '—' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مقبول ومكتمل</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">لا توجد عمليات تحويل مالي مسجلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Money Transfer Form (1 Col) -->
        <div class="glass-panel p-4 md:p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">
                <i class="fa-solid fa-paper-plane ml-1.5 text-[#D41414]"></i>تحويل نقدي جديد
            </h3>

            <form method="POST" action="{{ route('transfers.store') }}" class="space-y-4">
                @csrf

                <!-- From Wallet -->
                <div class="space-y-1">
                    <label for="from_wallet_id" class="block text-xs font-semibold text-gray-300">الخزينة المصدر (خصم منها) <span class="text-rose-500">*</span></label>
                    <select name="from_wallet_id" id="from_wallet_id" required class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                        <option value="">اختر الخزينة...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('from_wallet_id')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- To Wallet -->
                <div class="space-y-1">
                    <label for="to_wallet_id" class="block text-xs font-semibold text-gray-300">الخزينة المستهدفة (إيداع فيها) <span class="text-rose-500">*</span></label>
                    <select name="to_wallet_id" id="to_wallet_id" required class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                        <option value="">اختر الخزينة...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('to_wallet_id')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Amount -->
                <div class="space-y-1">
                    <label for="amount" class="block text-xs font-semibold text-gray-300">المبلغ المحول <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount" required placeholder="0.00" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('amount')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Notes -->
                <div class="space-y-1">
                    <label for="notes" class="block text-xs font-semibold text-gray-300">ملاحظات التحويل</label>
                    <input type="text" name="notes" id="notes" placeholder="مثال: توريد الأرباح الأسبوعية للخزينة الرئيسية" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('notes')" class="text-xs text-rose-500 mt-1" />
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                    تحويل وإيداع الآن <i class="fa-solid fa-paper-plane mr-1"></i>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>