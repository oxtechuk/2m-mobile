<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-pen-to-square ml-2 text-[#D41414]"></i>تعديل الفرع</h2>
                <p class="text-xs text-gray-500 mt-1">تحديث اسم الفرع، رقم الهاتف، أو العنوان بالتفصيل.</p>
            </div>
            
            @if($branch->id != 1)
                <!-- Delete Branch Form -->
                <form method="POST" action="{{ route('branches.destroy', $branch->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الفرع نهائياً؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center">
                        <i class="fa-solid fa-trash-can ml-1"></i> حذف الفرع
                    </button>
                </form>
            @endif
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('branches.update', $branch->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="space-y-1">
                <label for="name" class="block text-xs font-semibold text-gray-300">اسم الفرع <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    placeholder="اسم الفرع" 
                    value="{{ old('name', $branch->name) }}" 
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
                    placeholder="رقم الهاتف" 
                    value="{{ old('phone', $branch->phone) }}" 
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
                    placeholder="العنوان الجغرافي" 
                    value="{{ old('address', $branch->address) }}" 
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
                <x-input-error :messages="$errors->get('address')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    حفظ التعديلات <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>