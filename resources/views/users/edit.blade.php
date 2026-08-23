<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-user-gear ml-2 text-[#D41414]"></i>تعديل الموظف والصلاحيات</h2>
                <p class="text-xs text-gray-500 mt-1">تحديث تفاصيل الموظف أو تعديل دوره الوظيفي وحالة الحساب.</p>
            </div>
            <!-- Delete Button -->
            <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الموظف نهائياً؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center">
                    <i class="fa-solid fa-user-xmark ml-1"></i> حذف حساب الموظف
                </button>
            </form>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم الموظف بالكامل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        value="{{ old('name', $user->name) }}"
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
                        value="{{ old('phone', $user->phone) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs font-mono text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('phone')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-gray-300">البريد الإلكتروني (اسم المستخدم) <span class="text-rose-500">*</span></label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        value="{{ old('email', $user->email) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs text-left focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-semibold text-gray-300">تغيير كلمة المرور</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="اتركها فارغة إذا لم ترغب في التغيير"
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
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Branch Allocation -->
                <div class="space-y-1">
                    <label for="branch_id" class="block text-xs font-semibold text-gray-300">الفرع المسؤول عنه</label>
                    <select 
                        name="branch_id" 
                        id="branch_id" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                        <option value="">الإدارة العامة (الفرع الرئيسي)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $user->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
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
                        <label for="salary" class="block text-xs font-semibold text-gray-300">الراتب الأساسي الشهري (ج.م)</label>
                        <input 
                            type="number" 
                            step="0.5" 
                            min="0"
                            name="salary" 
                            id="salary" 
                            value="{{ old('salary', $user->salary ?? 0) }}"
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
                            value="{{ old('salary_payment_day', $user->salary_payment_day ?? 1) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-center text-xs focus:outline-none focus:border-amber-500"
                        >
                        <span class="text-[10px] text-gray-500">يوم 1 أو 25 من كل شهر</span>
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
                            value="{{ old('commission_rate', $user->commission_rate ?? 0) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-purple-400 font-mono font-bold text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('commission_rate')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- National ID -->
                    <div class="space-y-1">
                        <label for="national_id" class="block text-xs font-semibold text-gray-300">الرقم القومي</label>
                        <input 
                            type="text" 
                            name="national_id" 
                            id="national_id" 
                            value="{{ old('national_id', $user->national_id) }}"
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
                            value="{{ old('emergency_phone', $user->emergency_phone) }}"
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
                            value="{{ old('hire_date', $user->hire_date ? $user->hire_date->format('Y-m-d') : '') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-amber-500"
                        >
                        <x-input-error :messages="$errors->get('hire_date')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- Account active status -->
            <div class="space-y-1">
                <label for="is_active" class="block text-xs font-semibold text-gray-300">حالة نشاط الحساب <span class="text-rose-500">*</span></label>
                <select 
                    name="is_active" 
                    id="is_active" 
                    required 
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
                    <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>نشط (يسمح له بتسجيل الدخول)</option>
                    <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>معطل (يمنع من الدخول والعمل)</option>
                </select>
                <x-input-error :messages="$errors->get('is_active')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    تحديث البيانات والراتب <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>