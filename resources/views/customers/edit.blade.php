<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-user-pen ml-2 text-[#D41414]"></i>تعديل بيانات العميل</h2>
                <p class="text-xs text-gray-500 mt-1">تحديث بيانات العميل وتفاصيل الاتصال الخاصة به.</p>
            </div>
            <!-- Delete Button -->
            <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا العميل نهائياً؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center">
                    <i class="fa-solid fa-trash ml-1"></i> حذف العميل
                </button>
            </form>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('customers.update', $customer->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-gray-300">الاسم بالكامل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="أدخل اسم العميل بالكامل"
                        value="{{ old('name', $customer->name) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
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
                        value="{{ old('phone', $customer->phone) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('phone')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Secondary Phone -->
                <div class="space-y-1">
                    <label for="secondary_phone" class="block text-xs font-semibold text-gray-300">رقم هاتف إضافي</label>
                    <input 
                        type="text" 
                        name="secondary_phone" 
                        id="secondary_phone" 
                        placeholder="اختياري"
                        value="{{ old('secondary_phone', $customer->secondary_phone) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('secondary_phone')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-gray-300">البريد الإلكتروني</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="اختياري: email@example.com"
                        value="{{ old('email', $customer->email) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs text-left"
                    >
                    <x-input-error :messages="$errors->get('email')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- Branch Allocation -->
            <div class="space-y-1">
                <label for="branch_id" class="block text-xs font-semibold text-gray-300">فرع التسجيل <span class="text-rose-500">*</span></label>
                <select 
                    name="branch_id" 
                    id="branch_id" 
                    required 
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >
                    @php
                        $branches = \App\Models\Branch::all();
                    @endphp
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', $customer->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('branch_id')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Address -->
            <div class="space-y-1">
                <label for="address" class="block text-xs font-semibold text-gray-300">العنوان الكامل</label>
                <textarea 
                    name="address" 
                    id="address" 
                    rows="2" 
                    placeholder="العنوان السكني أو العمل"
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >{{ old('address', $customer->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Notes -->
            <div class="space-y-1">
                <label for="notes" class="block text-xs font-semibold text-gray-300">ملاحظات إضافية</label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="2" 
                    placeholder="ملاحظات وتفاصيل أخرى"
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >{{ old('notes', $customer->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    تحديث البيانات <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>