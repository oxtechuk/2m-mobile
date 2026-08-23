<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Expenses List (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-circle-minus ml-2 text-[#D41414]"></i>المصروفات اليومية للفرع</h2>
                    <p class="text-xs text-gray-500 mt-1">تسجيل ومتابعة المنصرفات النقدية الجارية خارج دورات البيع والصيانة.</p>
                </div>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('expenses.index') }}" class="relative shrink-0">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="البحث بالبيان..." 
                    value="{{ request('search') }}"
                    class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
            </form>

            <!-- Table of Expenses -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">البند / البيان</th>
                            <th class="pb-2">التصنيف</th>
                            <th class="pb-2">الخزينة المخصوم منها</th>
                            <th class="pb-2">المبلغ</th>
                            <th class="pb-2">التاريخ</th>
                            <th class="pb-2 text-left">خيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($expenses as $exp)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-semibold text-white">{{ $exp->description }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-white/5 border border-white/10 text-gray-300">
                                        {{ $exp->category }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-400">{{ $exp->wallet->name ?? '—' }}</td>
                                <td class="py-3 font-bold font-mono text-rose-500">- {{ number_format($exp->amount, 2) }} ج.م</td>
                                <td class="py-3 font-mono text-gray-500">{{ $exp->created_at->format('Y-m-d h:i A') }}</td>
                                <td class="py-3 text-left">
                                    <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء وحذف هذا المصروف وإعادة القيمة للخزينة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-white/5 hover:bg-rose-500/10 text-rose-500 rounded transition" title="حذف وإلغاء">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">لا توجد مصروفات مسجلة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Record Expense Form (1 Col) -->
        <div class="glass-panel p-4 md:p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">
                <i class="fa-solid fa-plus-circle ml-1.5 text-[#D41414]"></i>تسجيل بند مصروف جديد
            </h3>

            <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                @csrf

                <!-- Description -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-semibold text-gray-300">البيان / تفصيل المصروف <span class="text-rose-500">*</span></label>
                    <input type="text" name="description" id="description" required placeholder="مثال: فاتورة كهرباء، شراء شاي وضيافة" value="{{ old('description') }}" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('description')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Category -->
                <div class="space-y-1">
                    <label for="category" class="block text-xs font-semibold text-gray-300">تصنيف البند <span class="text-rose-500">*</span></label>
                    <select name="category" id="category" required class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                        <option value="ايجار وفواتير">إيجار، مياه وكهرباء (Utilities)</option>
                        <option value="رواتب وسلف">سلف ورواتب موظفين (Salaries)</option>
                        <option value="ضيافة ونظافة">ضيافة وبوفيه ونظافة</option>
                        <option value="دعاية واعلان">تسويق وإعلانات (Marketing)</option>
                        <option value="اخرى">مصروفات أخرى متنوعة</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Wallet -->
                <div class="space-y-1">
                    <label for="wallet_id" class="block text-xs font-semibold text-gray-300">الخزينة المخصوم منها <span class="text-rose-500">*</span></label>
                    <select name="wallet_id" id="wallet_id" required class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]">
                        <option value="">اختر الخزينة...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (الرصيد: {{ number_format($w->balance, 2) }} ج.م)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('wallet_id')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Amount -->
                <div class="space-y-1">
                    <label for="amount" class="block text-xs font-semibold text-gray-300">قيمة المبلغ المصروف <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount" required placeholder="0.00" class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]">
                    <x-input-error :messages="$errors->get('amount')" class="text-xs text-rose-500 mt-1" />
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                    تأكيد وتسجيل المصروف <i class="fa-solid fa-circle-minus mr-1"></i>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>