<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Page Header -->
        <div class="flex items-center justify-between glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-400"></i>
                    <span>إضافة مورد جديد</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">إدخال البيانات الأساسية والمالية للمورد وتأكيد الرصيد الافتتاحي.</p>
            </div>

            <a href="{{ route('suppliers.index') }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                رجوع للدليل <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>

        <!-- Form Panel -->
        <div class="glass-panel p-6">
            <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-semibold text-gray-300">اسم المورد / المسؤول <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="مثال: أحمد محمود (شركة الأمل)"
                            value="{{ old('name') }}"
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
                            placeholder="مثال: التيسير لقطع غيار الهواتف"
                            value="{{ old('company_name') }}"
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
                            placeholder="01000000000"
                            value="{{ old('phone') }}"
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
                            placeholder="supplier@example.com"
                            value="{{ old('email') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-left text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>

                <!-- Opening Balance -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-2">
                    <label for="opening_balance" class="block text-xs font-semibold text-gray-300">
                        <i class="fa-solid fa-scale-balanced text-amber-400 ml-1"></i> الرصيد الافتتاحي للسابقة المالية <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="opening_balance" 
                        id="opening_balance" 
                        required 
                        value="{{ old('opening_balance', '0.00') }}"
                        class="block w-full px-3 py-2 bg-[#050505] border border-white/10 rounded-xl text-white font-mono text-left text-xs focus:outline-none focus:border-indigo-500"
                    >
                    <p class="text-[10px] text-gray-500">
                        • أدخل قيمة موجب (+) إذا كان هناك <strong>دين مستحق للمورد</strong> مسبقاً قبل النظام.<br>
                        • أدخل قيمة سالب (-) إذا كان هناك <strong>رصيد مالي مدفوع للمورد</strong> (دائنية لصالح المحل).
                    </p>
                    <x-input-error :messages="$errors->get('opening_balance')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="address" class="block text-xs font-semibold text-gray-300">العنوان التفصيلي</label>
                    <input 
                        type="text" 
                        name="address" 
                        id="address" 
                        placeholder="المحافظة - المدينة - شارع..."
                        value="{{ old('address') }}"
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
                        placeholder="أي تفاصيل خاصة بتعاملات المورد، طريقة التوصيل، خصومات حصرية..."
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                    >{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-white/5 flex justify-end gap-2">
                    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                        إلغاء
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-lg">
                        حفظ المورد الجديد <i class="fa-solid fa-floppy-disk mr-1"></i>
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>
