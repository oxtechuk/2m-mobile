<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Side: List of Categories (2 columns on lg screens) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-tags ml-2 text-[#D41414]"></i>تصنيفات المنتجات</h2>
                    <p class="text-xs text-gray-500 mt-1">عرض وتقسيم المنتجات في أقسام منظمة للبيع السريع والفرز.</p>
                </div>
            </div>

            <!-- Search Field -->
            <form method="GET" action="{{ route('categories.index') }}" class="relative shrink-0">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="البحث باسم القسم..." 
                    value="{{ request('search') }}"
                    class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
            </form>

            <!-- Table of Categories -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="pb-2">اسم القسم</th>
                            <th class="pb-2">الوصف</th>
                            <th class="pb-2">المنتجات المرتبطة</th>
                            <th class="pb-2 text-left">خيارات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 font-semibold text-white">{{ $cat->name }}</td>
                                <td class="py-3 text-gray-400 max-w-xs truncate">{{ $cat->description ?? 'لا يوجد وصف للقسم' }}</td>
                                <td class="py-3 font-bold font-mono text-emerald-400">{{ $cat->products_count }} منتج</td>
                                <td class="py-3 text-left">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('categories.edit', $cat->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded transition" title="تعديل">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $cat->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا القسم؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-white/5 hover:bg-rose-500/10 text-rose-500 rounded transition" title="حذف">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500">لا توجد أقسام مسجلة مطابقة للبحث.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Add New Category Form (1 column on lg screens) -->
        <div class="glass-panel p-4 md:p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">
                <i class="fa-solid fa-plus-circle ml-1.5 text-[#D41414]"></i>إضافة قسم جديد
            </h3>

            <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf

                <!-- Category Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم القسم <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="مثال: هواتف ذكية، قطع غيار شاشات"
                        value="{{ old('name') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Description -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-semibold text-gray-300">الوصف</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="3" 
                        placeholder="نبذة مختصرة عن هذا القسم..."
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="text-xs text-rose-500 mt-1" />
                </div>

                <button type="submit" class="w-full py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-lg text-xs transition shadow-lg glow-primary">
                    حفظ وإضافة القسم <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </form>
        </div>

    </div>
</x-app-layout>