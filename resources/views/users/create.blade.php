<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-user-plus ml-2 text-[#D41414]"></i>إضافة موظف جديد</h2>
            <p class="text-xs text-gray-500 mt-1">تعبئة بيانات الحساب وتعيين الدور الصلاحيات والفرع المسؤول عنه.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم الموظف بالكامل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="أدخل الاسم ثلاثي"
                        value="{{ old('name') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-semibold text-gray-300">رقم الهاتف الرئيسي <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        required 
                        placeholder="مثال: 01012345678"
                        value="{{ old('phone') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs font-mono text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('phone')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-gray-300">البريد الإلكتروني (لتسجيل الدخول) <span class="text-rose-500">*</span></label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        placeholder="email@2m.com"
                        value="{{ old('email') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-semibold text-gray-300">كلمة المرور المؤقتة <span class="text-rose-500">*</span></label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('password')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Role selection -->
                <div class="space-y-1">
                    <label for="role" class="block text-xs font-semibold text-gray-300">دور الصلاحية الوظيفي <span class="text-rose-500">*</span></label>
                    <select 
                        name="role" 
                        id="role" 
                        required 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                        <option value="">اختر الصلاحية...</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Branch Allocation -->
                <div class="space-y-1">
                    <label for="branch_id" class="block text-xs font-semibold text-gray-300">الفرع التابع له <span class="text-rose-500">*</span></label>
                    <select 
                        name="branch_id" 
                        id="branch_id" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                        <option value="">الإدارة العامة (الفرع الرئيسي)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('branch_id')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- HR & Salary Section Header -->
            <div class="pt-4 border-t border-white/5">
                <h3 class="text-xs font-bold text-amber-500 flex items-center gap-1.5 mb-3">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <span>بيانات الراتب والتعيين وشؤون الموظفين:</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Basic Salary -->
                    <div class="space-y-1">
                        <label for="salary" class="block text-xs font-semibold text-gray-300">الراتب الأساسي الشهري (ج.م) <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            step="0.5" 
                            min="0"
                            name="salary" 
                            id="salary" 
                            placeholder="5000.00"
                            value="{{ old('salary', 0) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-emerald-400 font-mono font-bold text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('salary')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Salary Payment Day -->
                    <div class="space-y-1">
                        <label for="salary_payment_day" class="block text-xs font-semibold text-gray-300">يوم صرف الراتب في الشهر</label>
                        <input 
                            type="number" 
                            min="1" 
                            max="31"
                            name="salary_payment_day" 
                            id="salary_payment_day" 
                            placeholder="1"
                            value="{{ old('salary_payment_day', 1) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-center text-xs focus:outline-none focus:border-amber-500"
                        >
                        <span class="text-[10px] text-gray-500">مثال: يوم 1 أو 25 من كل شهر</span>
                        <x-input-error :messages="$errors->get('salary_payment_day')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Commission Rate % -->
                    <div class="space-y-1">
                        <label for="commission_rate" class="block text-xs font-semibold text-gray-300">نسبة العمولة (%)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            min="0" 
                            max="100"
                            name="commission_rate" 
                            id="commission_rate" 
                            placeholder="0.0"
                            value="{{ old('commission_rate', 0) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-purple-400 font-mono font-bold text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('commission_rate')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- National ID -->
                    <div class="space-y-1">
                        <label for="national_id" class="block text-xs font-semibold text-gray-300">الرقم القومي (14 رقم)</label>
                        <input 
                            type="text" 
                            name="national_id" 
                            id="national_id" 
                            placeholder="29801011234567"
                            value="{{ old('national_id') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('national_id')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Emergency Phone -->
                    <div class="space-y-1">
                        <label for="emergency_phone" class="block text-xs font-semibold text-gray-300">هاتف الطوارئ / قريب</label>
                        <input 
                            type="text" 
                            name="emergency_phone" 
                            id="emergency_phone" 
                            placeholder="01198765432"
                            value="{{ old('emergency_phone') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('emergency_phone')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Hire Date -->
                    <div class="space-y-1">
                        <label for="hire_date" class="block text-xs font-semibold text-gray-300">تاريخ التعيين</label>
                        <input 
                            type="date" 
                            name="hire_date" 
                            id="hire_date" 
                            value="{{ old('hire_date', date('Y-m-d')) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('hire_date')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    حفظ حساب وراتب الموظف <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>