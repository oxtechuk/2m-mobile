<x-app-layout>
    <div class="max-w-md mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-3 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-tags ml-2 text-[#D41414]"></i>تعديل القسم</h2>
                <p class="text-xs text-gray-500 mt-1">تحديث اسم القسم والوصف في المخزون.</p>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('categories.update', $category->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="space-y-1">
                <label for="name" class="block text-xs font-semibold text-gray-300">اسم القسم <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    required 
                    value="{{ old('name', $category->name) }}"
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
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
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >{{ old('description', $category->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-2 space-x-reverse">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    تحديث القسم <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>
