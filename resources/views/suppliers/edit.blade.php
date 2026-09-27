<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Page Header -->
        <div class="flex items-center justify-between glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-indigo-400"></i>
                    <span>تعديل بيانات المورد: {{ $supplier->name }}</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">تحديث معلومات الاتصال والاسم وحالة التفعيل للمورد.</p>
            </div>

            <a href="{{ route('suppliers.index') }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                رجوع للدليل <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>

        <!-- Form Panel -->
        <div class="glass-panel p-6">
            <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-semibold text-gray-300">اسم المورد / المسؤول <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $supplier->name) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Company Name -->
                    <div class="space-y-1">
                        <label for="company_name" class="block text-xs font-semibold text-gray-300">اسم الشركة / المعرض التجاري</label>
                        <input 
                            type="text" 
                            name="company_name" 
                            id="company_name" 
                            value="{{ old('company_name', $supplier->company_name) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('company_name')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div class="space-y-1">
                        <label for="phone" class="block text-xs font-semibold text-gray-300">رقم الهاتف / الواتساب</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $supplier->phone) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-left text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('phone')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-semibold text-gray-300">البريد الإلكتروني</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', $supplier->email) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-left text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>

                <!-- Status Toggle -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-white">حالة الحساب والتوريد</span>
                        <span class="text-[10px] text-gray-400">عند تعطيل المورد، لن يظهر بالقائمة المتاحة لفواتير الشراء الجديدة.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="address" class="block text-xs font-semibold text-gray-300">العنوان التفصيلي</label>
                    <input 
                        type="text" 
                        name="address" 
                        id="address" 
                        value="{{ old('address', $supplier->address) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                    >
                    <x-input-error :messages="$errors->get('address')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Notes -->
                <div class="space-y-1">
                    <label for="notes" class="block text-xs font-semibold text-gray-300">ملاحظات إضافية</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                    >{{ old('notes', $supplier->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-white/5 flex justify-end gap-2">
                    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                        إلغاء
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-lg">
                        حفظ التعديلات <i class="fa-solid fa-floppy-disk mr-1"></i>
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>
