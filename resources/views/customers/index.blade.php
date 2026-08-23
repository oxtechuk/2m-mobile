<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-white/5 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-users-gear ml-2 text-[#D41414]"></i>إدارة العملاء</h2>
                <p class="text-xs text-gray-500 mt-1">تسجيل وتعديل بيانات العملاء ومتابعة نقاط الولاء وتاريخ عمليات الشراء والصيانة.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-lg transition shadow-lg flex items-center">
                <i class="fa-solid fa-user-plus ml-1"></i> إضافة عميل جديد
            </a>
        </div>

        @php
            $customers = \App\Models\Customer::with('branch')->get();
            $totalCustomers = $customers->count();
            $totalPoints = $customers->sum('loyalty_points');
            $totalPurchases = $customers->sum('total_purchases');
        @endphp

        <!-- Quick Stats Widgets -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="glass-panel p-4 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">إجمالي العملاء</p>
                    <h3 class="text-xl font-bold text-white mt-1">{{ $totalCustomers }} <span class="text-xs font-normal text-gray-500">عملاء</span></h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <div class="glass-panel p-4 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">نقاط الولاء الموزعة</p>
                    <h3 class="text-xl font-bold text-white mt-1">{{ number_format($totalPoints) }} <span class="text-xs font-normal text-gray-500">نقطة</span></h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <div class="glass-panel p-4 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">إجمالي مبيعات العملاء</p>
                    <h3 class="text-xl font-bold text-white mt-1">{{ number_format($totalPurchases, 2) }} <span class="text-xs font-normal text-gray-500">ج.م</span></h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="glass-panel p-4">
            <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="البحث باسم العميل أو رقم الهاتف..." 
                        value="{{ request('search') }}"
                        class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    تصفية نتائج البحث
                </button>
            </form>
        </div>

        <!-- Customers Listing -->
        <div class="glass-panel p-4 md:p-6">
            <!-- Desktop view table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">اسم العميل</th>
                            <th class="pb-2">رقم الهاتف الرئيسي</th>
                            <th class="pb-2">رقم الهاتف الإضافي</th>
                            <th class="pb-2">الفرع</th>
                            <th class="pb-2">نقاط الولاء</th>
                            <th class="pb-2">إجمالي المشتريات</th>
                            <th class="pb-2">إجمالي الصيانة</th>
                            <th class="pb-2 text-left">الخيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($customers as $cust)
                            <tr class="hover:bg-white/5">
                                <td class="py-3 font-semibold text-white">{{ $cust->name }}</td>
                                <td class="py-3 font-mono">{{ $cust->phone }}</td>
                                <td class="py-3 font-mono text-gray-400">{{ $cust->secondary_phone ?? '—' }}</td>
                                <td class="py-3 text-gray-300">{{ $cust->branch->name ?? 'غير محدد' }}</td>
                                <td class="py-3 font-bold text-amber-400">{{ $cust->loyalty_points }}</td>
                                <td class="py-3 font-bold text-emerald-400">{{ number_format($cust->total_purchases, 2) }} ج.م</td>
                                <td class="py-3 font-bold text-blue-400">{{ number_format($cust->total_repairs, 2) }} ج.م</td>
                                <td class="py-3 text-left">
                                    <div class="flex items-center justify-end space-x-2 space-x-reverse">
                                        <a href="{{ route('customers.edit', $cust->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 rounded text-gray-300 transition" title="تعديل البيانات">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">لا يوجد عملاء مطابقين لعملية البحث حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile view cards list -->
            <div class="block md:hidden space-y-3">
                @forelse($customers as $cust)
                    <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-bold text-white">{{ $cust->name }}</h4>
                            <span class="px-2 py-0.5 text-[9px] rounded bg-amber-500/10 border border-amber-500/20 text-amber-400">
                                <i class="fa-solid fa-star text-[8px] ml-0.5"></i> {{ $cust->loyalty_points }} نقطة
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-mono"><i class="fa-solid fa-phone text-[8px] ml-1"></i> الهاتف: {{ $cust->phone }}</p>
                        
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/5 text-[10px]">
                            <div>
                                <span class="text-gray-500 block">المشتريات</span>
                                <span class="text-emerald-400 font-bold font-mono">{{ number_format($cust->total_purchases, 2) }} ج.م</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">الصيانة</span>
                                <span class="text-blue-400 font-bold font-mono">{{ number_format($cust->total_repairs, 2) }} ج.م</span>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2 border-t border-white/5">
                            <a href="{{ route('customers.edit', $cust->id) }}" class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-white text-[10px] hover:bg-white/10 transition flex items-center gap-1">
                                <i class="fa-solid fa-user-pen text-[9px]"></i> تعديل العميل
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 text-xs">لا يوجد عملاء مطابقين لعملية البحث حالياً.</div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>