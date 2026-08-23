<x-app-layout>
    <div 
        class="max-w-3xl mx-auto glass-panel p-6 space-y-6" 
        x-data="{
            quickCustomerModal: false,
            isSavingCustomer: false,
            customerError: '',
            newCust: {
                name: '',
                phone: '',
                secondary_phone: '',
                address: '',
                notes: ''
            },
            async saveQuickCustomer() {
                if (!this.newCust.name || !this.newCust.phone) {
                    this.customerError = 'يرجى إدخال اسم العميل ورقم الهاتف.';
                    return;
                }
                this.isSavingCustomer = true;
                this.customerError = '';
                try {
                    const res = await fetch('{{ route('customers.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        },
                        body: JSON.stringify(this.newCust)
                    });
                    const data = await res.json();
                    if (res.ok && data.success && data.customer) {
                        const selectEl = document.getElementById('customer_id');
                        const opt = document.createElement('option');
                        opt.value = data.customer.id;
                        opt.textContent = `${data.customer.name} (${data.customer.phone})`;
                        opt.selected = true;
                        selectEl.appendChild(opt);
                        this.quickCustomerModal = false;
                        this.newCust = { name: '', phone: '', secondary_phone: '', address: '', notes: '' };
                        alert('✅ تم إضافة وتحديد العميل (' + data.customer.name + ') بنجاح!');
                    } else {
                        this.customerError = data.message || 'رقم الهاتف مستخدم بالفعل أو حدث خطأ بالإدخال.';
                    }
                } catch(err) {
                    this.customerError = 'حدث خطأ بالاتصال: ' + err.message;
                } finally {
                    this.isSavingCustomer = false;
                }
            }
        }"
    >
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-plus ml-2 text-[#D41414]"></i>تسجيل جهاز صيانة جديد</h2>
            <p class="text-xs text-gray-500 mt-1">تعبئة بيانات استلام جهاز العميل وتحديد الحالة الأولية والأعطال.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('maintenance.store') }}" class="space-y-6">
            @csrf

            <!-- Client & Technician -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer Selection with Quick Add -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="customer_id" class="block text-xs font-semibold text-gray-300">العميل المستلم منه <span class="text-rose-500">*</span></label>
                        <button 
                            type="button" 
                            @click="quickCustomerModal = true; customerError = '';" 
                            class="text-[11px] text-[#D41414] hover:text-rose-400 font-bold flex items-center gap-1 hover:underline"
                        >
                            <i class="fa-solid fa-user-plus text-[10px]"></i> + إضافة عميل سريع
                        </button>
                    </div>
                    <select 
                        name="customer_id" 
                        id="customer_id" 
                        required 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                        <option value="">اختر العميل</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->phone }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('customer_id')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Technician Assignment -->
                <div class="space-y-1">
                    <label for="technician_id" class="block text-xs font-semibold text-gray-300">إسناد لفني الصيانة</label>
                    <select 
                        name="technician_id" 
                        id="technician_id" 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                        <option value="">غير معين (إسناد لاحقاً)</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('technician_id')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- Device Specs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Device Type -->
                <div class="space-y-1">
                    <label for="device_type" class="block text-xs font-semibold text-gray-300">نوع الجهاز <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="device_type" 
                        id="device_type" 
                        required 
                        placeholder="مثال: هاتف، تابلت، ساعة"
                        value="{{ old('device_type', 'هاتف ذكي') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                    <x-input-error :messages="$errors->get('device_type')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Device Model -->
                <div class="space-y-1">
                    <label for="device_model" class="block text-xs font-semibold text-gray-300">موديل الجهاز <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="device_model" 
                        id="device_model" 
                        required 
                        placeholder="مثال: iPhone 14 Pro Max"
                        value="{{ old('device_model') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                    <x-input-error :messages="$errors->get('device_model')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Serial / IMEI -->
                <div class="space-y-1">
                    <label for="device_serial" class="block text-xs font-semibold text-gray-300">الرقم التسلسلي / IMEI</label>
                    <input 
                        type="text" 
                        name="device_serial" 
                        id="device_serial" 
                        placeholder="أدخل السيريال أو الـ IMEI"
                        value="{{ old('device_serial') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('device_serial')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- Pre-repair checklist (تأكيد حالة الجهاز قبل الاستلام) -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-gray-300">معاينة حالة الجهاز قبل الاستلام (التشيك ليست):</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-[#0a0a0a] border border-white/5 p-4 rounded-xl">
                    @php
                        $checks = [
                            'power' => 'الجهاز يفتح باور',
                            'touch' => 'اللمس يعمل بالكامل',
                            'screen' => 'الشاشة سليمة لا كسر فيها',
                            'camera_front' => 'الكاميرا الأمامية',
                            'camera_back' => 'الكاميرا الخلفية',
                            'wifi' => 'الواي فاي والشبكة',
                            'charging' => 'منفذ الشحن يعمل',
                            'scratches' => 'يوجد خدوش/كسور بالظهر'
                        ];
                    @endphp
                    @foreach($checks as $key => $label)
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <input 
                                type="checkbox" 
                                name="pre_repair_checklist[{{ $key }}]" 
                                id="check_{{ $key }}" 
                                value="ok"
                                class="rounded bg-[#0a0a0a] border-white/10 text-[#D41414] focus:ring-[#D41414]"
                            >
                            <label for="check_{{ $key }}" class="text-[11px] text-gray-400 select-none">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Problem & Cost details -->
            <div class="space-y-4">
                <!-- Description -->
                <div class="space-y-1">
                    <label for="problem_description" class="block text-xs font-semibold text-gray-300">وصف العطل كما يذكره العميل <span class="text-rose-500">*</span></label>
                    <textarea 
                        name="problem_description" 
                        id="problem_description" 
                        rows="2" 
                        required
                        placeholder="تفاصيل العطل والشكوى..."
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >{{ old('problem_description') }}</textarea>
                    <x-input-error :messages="$errors->get('problem_description')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <!-- Estimated Cost -->
                    <div class="space-y-1">
                        <label for="estimated_cost" class="block text-xs font-semibold text-gray-300">التكلفة التقريبية المتوقعة</label>
                        <input 
                            type="number" 
                            step="0.01"
                            name="estimated_cost" 
                            id="estimated_cost" 
                            placeholder="0.00"
                            value="{{ old('estimated_cost') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                        >
                        <x-input-error :messages="$errors->get('estimated_cost')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Advance Payment -->
                    <div class="space-y-1">
                        <label for="advance_payment" class="block text-xs font-semibold text-gray-300">المبلغ المدفوع مقدماً</label>
                        <input 
                            type="number" 
                            step="0.01"
                            name="advance_payment" 
                            id="advance_payment" 
                            placeholder="0.00"
                            value="{{ old('advance_payment', 0) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                        >
                        <x-input-error :messages="$errors->get('advance_payment')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Priority -->
                    <div class="space-y-1">
                        <label for="priority" class="block text-xs font-semibold text-gray-300">الأولوية <span class="text-rose-500">*</span></label>
                        <select 
                            name="priority" 
                            id="priority" 
                            required 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                        >
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>عادي</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>مرتفع</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجل جداً</option>
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Estimated Delivery -->
                    <div class="space-y-1">
                        <label for="estimated_delivery" class="block text-xs font-semibold text-gray-300">موعد التسليم المتوقع</label>
                        <input 
                            type="date" 
                            name="estimated_delivery" 
                            id="estimated_delivery" 
                            value="{{ old('estimated_delivery') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs text-left"
                        >
                        <x-input-error :messages="$errors->get('estimated_delivery')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    تسجيل الجهاز <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

        <!-- Quick Add Customer Modal -->
        <div 
            x-show="quickCustomerModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            style="display: none;"
            @keydown.escape.window="quickCustomerModal = false"
        >
            <div 
                class="bg-[#18181b] border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl text-right"
                @click.outside="quickCustomerModal = false"
            >
                <div class="bg-[#D41414] text-white p-4 flex items-center justify-between font-bold">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة عميل جديد سريعاً</span>
                    </h3>
                    <button @click="quickCustomerModal = false" class="text-white/80 hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="saveQuickCustomer()" class="p-5 space-y-3.5">
                    <!-- Customer Name -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">اسم العميل <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            x-model="newCust.name"
                            required
                            placeholder="مثال: محمد السيد" 
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-lg px-3 py-2 text-xs text-white placeholder-gray-600 focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414]"
                        >
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">رقم الهاتف الأساسي <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            x-model="newCust.phone"
                            required
                            placeholder="مثال: 01012345678" 
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-lg px-3 py-2 text-xs text-white placeholder-gray-600 font-mono text-left focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414]"
                        >
                    </div>

                    <!-- Secondary Phone -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">رقم هاتف إضافي (اختياري)</label>
                        <input 
                            type="text" 
                            x-model="newCust.secondary_phone"
                            placeholder="مثال: 01212345678" 
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-lg px-3 py-2 text-xs text-white placeholder-gray-600 font-mono text-left focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414]"
                        >
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">العنوان / المنطقة (اختياري)</label>
                        <input 
                            type="text" 
                            x-model="newCust.address"
                            placeholder="مثال: الجيزة - الدقي" 
                            class="w-full bg-[#0a0a0a] border border-white/10 rounded-lg px-3 py-2 text-xs text-white placeholder-gray-600 focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414]"
                        >
                    </div>

                    <!-- Error Alert -->
                    <div x-show="customerError" class="p-2.5 rounded-lg bg-rose-950/40 border border-rose-800 text-rose-400 text-xs" x-text="customerError"></div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-white/10 flex justify-end gap-2">
                        <button 
                            type="button"
                            @click="quickCustomerModal = false" 
                            class="px-4 py-2 rounded-lg border border-white/10 text-xs font-bold text-gray-300 hover:bg-white/5"
                        >
                            إلغاء
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2 glow-primary"
                            :disabled="isSavingCustomer"
                        >
                            <i x-show="!isSavingCustomer" class="fa-solid fa-check"></i>
                            <i x-show="isSavingCustomer" class="fa-solid fa-spinner animate-spin"></i>
                            <span>حفظ واختيار العميل</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>