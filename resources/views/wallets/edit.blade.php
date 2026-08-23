<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-pen-to-square ml-2 text-[#D41414]"></i>تعديل بيانات الخزينة</h2>
            <p class="text-xs text-gray-500 mt-1">تحديث اسم الخزينة/المحفظة أو تغيير حالة نشاطها بالفرع.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('wallets.update', $wallet->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="space-y-1">
                <label for="name" class="block text-xs font-semibold text-gray-300">اسم الخزينة / المحفظة <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    placeholder="مثال: درج الكاشير الرئيسي"
                    value="{{ old('name', $wallet->name) }}"
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
                <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- is_active toggle -->
            <div class="space-y-1">
                <label for="is_active" class="block text-xs font-semibold text-gray-300">حالة نشاط الخزينة <span class="text-rose-500">*</span></label>
                <select 
                    name="is_active" 
                    id="is_active" 
                    required 
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
                    <option value="1" {{ old('is_active', $wallet->is_active) == '1' ? 'selected' : '' }}>نشطة (متاحة للإيداع والبيع والمصروفات)</option>
                    <option value="0" {{ old('is_active', $wallet->is_active) == '0' ? 'selected' : '' }}>معطلة (موقوفة مؤقتاً)</option>
                </select>
                <x-input-error :messages="$errors->get('is_active')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('wallets.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    حفظ التغييرات <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>
