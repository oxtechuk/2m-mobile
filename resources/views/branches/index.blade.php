<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Listing of branches (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-code-branch ml-2 text-[#D41414]"></i>إدارة فروع المؤسسة</h2>
                    <p class="text-xs text-gray-500 mt-1">عرض وتعديل فروع الشركة وهواتفها وعناوينها الجغرافية.</p>
                </div>
            </div>

            <!-- Branches List -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">اسم الفرع</th>
                            <th class="pb-2">رقم الهاتف</th>
                            <th class="pb-2">العنوان الجغرافي</th>
                            <th class="pb-2">تاريخ الإضافة</th>
                            <th class="pb-2 text-left">خيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($branches as $branch)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-semibold text-white">
                                    {{ $branch->name }}
                                    @if($branch->id == 1)
                                        <span class="px-1.5 py-0.5 rounded text-[8px] bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 mr-1.5">الرئيسي</span>
                                    @endif
                                </td>
                                <td class="py-3 font-mono text-gray-300">{{ $branch->phone ?? '—' }}</td>
                                <td class="py-3 text-gray-400">{{ $branch->address ?? '—' }}</td>
                                <td class="py-3 font-mono text-gray-500">{{ $branch->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 text-left">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('branches.edit', $branch->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded transition" title="تعديل الفرع">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">لا توجد فروع مضافة بالنظام حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Register New Branch Form (1 Col) -->
        <div class="glass-panel p-4 md:p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">
                <i class="fa-solid fa-plus-circle ml-1.5 text-[#D41414]"></i>إضافة فرع جديد للمؤسسة
            </h3>

            <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم الفرع <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="مثال: فرع الدقي، فرع مدينة نصر" 
                        value="{{ old('name') }}" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-semibold text-gray-300">رقم هاتف الفرع</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        placeholder="مثال: 0233445566" 
                        value="{{ old('phone') }}" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs font-mono text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('phone')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="address" class="block text-xs font-semibold text-gray-300">عنوان الفرع بالتفصيل</label>
                    <input 
                        type="text" 
                        name="address" 
                        id="address" 
                        placeholder="مثال: 15 شارع التحرير، الدقي، الجيزة" 
                        value="{{ old('address') }}" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('address')" class="text-xs text-rose-500 mt-1" />
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                    تأكيد وإضافة الفرع <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>