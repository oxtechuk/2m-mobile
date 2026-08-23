<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: List of Wallets/Cash Drawers (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-vault ml-2 text-[#D41414]"></i>الخزائن والمحافظ المالية</h2>
                    <p class="text-xs text-gray-500 mt-1">إدارة السيولة النقدية والمحافظ الإلكترونية للفرع الحالي.</p>
                </div>
            </div>

            <!-- Wallets Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($wallets as $wallet)
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-2xl flex flex-col justify-between space-y-4 hover:border-[#D41414]/30 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-white/5 text-gray-400 font-bold uppercase tracking-wider">
                                    {{ $wallet->type }}
                                </span>
                                <h4 class="text-sm font-bold text-white mt-1.5">{{ $wallet->name }}</h4>
                            </div>
                            
                            @php
                                $typeIcons = [
                                    'cash' => 'fa-cash-register text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                    'vodafone_cash' => 'fa-mobile-screen text-rose-500 bg-rose-500/10 border-rose-500/20',
                                    'instapay' => 'fa-bolt text-purple-400 bg-purple-500/10 border-purple-500/20',
                                    'bank' => 'fa-building-columns text-blue-400 bg-blue-500/10 border-blue-500/20',
                                ];
                                $iconClass = $typeIcons[$wallet->type] ?? 'fa-wallet text-gray-400 bg-white/5 border-white/10';
                            @endphp
                            
                            <div class="w-9 h-9 rounded-lg border flex items-center justify-center {{ $iconClass }}">
                                <i class="fa-solid {{ strtok($iconClass, ' ') }}"></i>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="text-[10px] text-gray-500 block">الرصيد المتوفر</span>
                            <span class="text-xl font-black text-white font-mono">{{ number_format($wallet->balance, 2) }} <span class="text-xs font-normal text-gray-500">{{ setting('default_currency', 'ج.م') }}</span></span>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-white/5">
                            <span class="text-[10px] text-gray-400">الفرع: {{ $wallet->branch->name ?? 'العام' }}</span>
                            <a href="{{ route('wallets.show', $wallet->id) }}" class="text-xs text-[#D41414] hover:underline flex items-center gap-1 font-bold">
                                كشف حركة <i class="fa-solid fa-chevron-left text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 text-xs">لا توجد محافظ مسجلة لهذا الفرع.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Register New Wallet Form (1 Col) -->
        <div class="glass-panel p-4 md:p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">
                <i class="fa-solid fa-plus-circle ml-1.5 text-[#D41414]"></i>إضافة خزينة/محفظة جديدة
            </h3>

            <form method="POST" action="{{ route('wallets.store') }}" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم الخزينة/المحفظة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required placeholder="مثال: درج كاشير الفرع، فودافون كاش 010..." value="{{ old('name') }}" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1">
                    <label for="type" class="block text-xs font-semibold text-gray-300">نوع الحساب <span class="text-rose-500">*</span></label>
                    <select name="type" id="type" required class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                        <option value="cash">درج كاشير نقدي (Cash Drawer)</option>
                        <option value="vodafone_cash">محفظة إلكترونية (Vodafone Cash)</option>
                        <option value="instapay">إنستا باي (Instapay)</option>
                        <option value="bank">حساب بنكي (Bank Account)</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="space-y-1">
                    <label for="balance" class="block text-xs font-semibold text-gray-300">الرصيد الافتتاحي المبدئي <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="balance" id="balance" required placeholder="0.00" value="{{ old('balance', 0) }}" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('balance')" class="text-xs text-rose-500 mt-1" />
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                    تأكيد وإنشاء الخزينة <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>