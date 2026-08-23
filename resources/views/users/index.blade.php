<x-app-layout>
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="glass-panel p-4 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-[#D41414]/15 border border-[#D41414]/25 flex items-center justify-center text-[#D41414] text-xl font-bold">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">إدارة شؤون الموظفين والفروع والرواتب</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">ملفات الموظفين، تحديد الرواتب الأساسية، ربط الفروع، وإدارة الصلاحيات.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Monthly Payroll Link -->
                <a href="{{ route('payroll.index') }}" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold rounded-xl transition border border-white/10 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-[#D41414]"></i>
                    <span>مسير الرواتب</span>
                </a>

                <!-- Adjustments Link -->
                <a href="{{ route('payroll.adjustments') }}" class="px-4 py-2.5 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white text-xs font-bold rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>السلف والخصومات</span>
                </a>

                <!-- Add Employee Button -->
                <a href="{{ route('users.create') }}" class="px-4 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-xl transition shadow-lg flex items-center gap-2 glow-primary">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>إضافة موظف جديد</span>
                </a>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-panel p-4 md:p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 pb-2">
                            <th class="py-2.5 pr-3">الموظف</th>
                            <th class="py-2.5">الفرع التابع له</th>
                            <th class="py-2.5">الوظيفة / الدور</th>
                            <th class="py-2.5 text-center">الراتب الأساسي</th>
                            <th class="py-2.5 text-center">موعد الصرف</th>
                            <th class="py-2.5 text-center">سلف معلقة</th>
                            <th class="py-2.5 text-center">حالة الحساب</th>
                            <th class="py-2.5 text-center">خيارات وإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                
                                <!-- Employee Info -->
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center font-bold text-gray-700 dark:text-gray-300 shrink-0">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $user->id) }}" class="font-bold text-gray-900 dark:text-white hover:text-[#D41414] transition block">
                                                {{ $user->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 font-mono">{{ $user->phone }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Branch -->
                                <td class="py-3 text-gray-700 dark:text-gray-300 font-bold">
                                    {{ $user->branch->name ?? 'الإدارة العامة' }}
                                </td>

                                <!-- Role -->
                                <td class="py-3">
                                    @php
                                        $roleBadges = [
                                            'admin' => 'bg-rose-500/10 border-rose-500/20 text-rose-500 dark:text-rose-400',
                                            'branch_manager' => 'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400',
                                            'cashier' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400',
                                            'technician' => 'bg-blue-500/10 border-blue-500/20 text-blue-600 dark:text-blue-400',
                                            'customer_service' => 'bg-purple-500/10 border-purple-500/20 text-purple-600 dark:text-purple-400',
                                        ];
                                        $roleNames = [
                                            'admin' => 'مدير عام',
                                            'branch_manager' => 'مدير فرع',
                                            'cashier' => 'كاشير مبيعات',
                                            'technician' => 'فني صيانة',
                                            'customer_service' => 'خدمة عملاء',
                                        ];
                                        $badgeClass = $roleBadges[$user->role] ?? 'bg-white/5 text-gray-400';
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] rounded-full border font-bold {{ $badgeClass }}">
                                        {{ $roleNames[$user->role] ?? $user->role }}
                                    </span>
                                </td>

                                <!-- Salary -->
                                <td class="py-3 text-center font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($user->salary ?? 0, 2) }} ج.م
                                </td>

                                <!-- Payment Day -->
                                <td class="py-3 text-center text-gray-600 dark:text-gray-300 font-mono">
                                    يوم {{ $user->salary_payment_day ?? 1 }}
                                </td>

                                <!-- Pending Advances -->
                                <td class="py-3 text-center font-mono font-bold text-amber-500">
                                    @php $advances = $user->pendingAdvancesTotal(); @endphp
                                    @if($advances > 0)
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                            {{ number_format($advances, 2) }} ج.م
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-[11px]">—</span>
                                    @endif
                                </td>

                                <!-- Active Status -->
                                <td class="py-3 text-center">
                                    @if($user->is_active)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-[10px]">
                                            نشط
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-500 font-bold text-[10px]">
                                            معطل
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('users.show', $user->id) }}" class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg transition" title="عرض ملف الموظف">
                                            <i class="fa-solid fa-id-card text-xs"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg transition" title="تعديل الموظف والراتب">
                                            <i class="fa-solid fa-user-pen text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">لا يوجد موظفين مسجلين حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>