<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Settings Form (2 Cols) -->
        <div class="lg:col-span-2 glass-panel p-4 md:p-6 space-y-6">
            <div class="border-b border-white/5 pb-4">
                <h2 class="text-base md:text-lg font-bold text-white"><i class="fa-solid fa-sliders ml-2 text-[#D41414]"></i>إعدادات النظام وتخصيص الموقع</h2>
                <p class="text-xs text-gray-500 mt-1">تعديل إعدادات المؤسسة، رفع الشعار، واختيار اللغات والسمات الافتراضية للنظام.</p>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Logo Section -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-3">
                    <label class="block text-xs font-semibold text-gray-300">شعار النظام / الأيقونة</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl border border-white/10 bg-white/5 flex items-center justify-center overflow-hidden">
                            @if(!empty($settings['store_logo']))
                                <img src="{{ asset('storage/' . $settings['store_logo']) }}" class="w-full h-full object-cover" alt="الشعار الحالي">
                            @else
                                <i class="fa-solid fa-image text-2xl text-gray-500"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="store_logo" 
                                id="store_logo"
                                accept="image/*"
                                class="block w-full text-xs text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#D41414]/10 file:text-[#D41414] hover:file:bg-[#D41414]/20 cursor-pointer"
                            >
                            <span class="text-[9px] text-gray-500 mt-1 block">يدعم صيغ الصور (PNG, JPG, SVG) بحد أقصى 2 ميجابايت.</span>
                            <x-input-error :messages="$errors->get('store_logo')" class="text-xs text-rose-500 mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Default Product Image Section -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-3">
                    <label class="block text-xs font-semibold text-gray-300">الصورة الموحدة الافتراضية للمنتجات بدون صورة</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl border border-white/10 bg-white/5 flex items-center justify-center overflow-hidden shrink-0">
                            @if(!empty($settings['default_product_image']))
                                <img src="{{ asset('storage/' . $settings['default_product_image']) }}" class="w-full h-full object-cover" alt="الصورة الافتراضية">
                            @else
                                <i class="fa-solid fa-box text-2xl text-gray-500"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input 
                                type="file" 
                                name="default_product_image" 
                                id="default_product_image"
                                accept="image/*"
                                class="block w-full text-xs text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/10 file:text-emerald-500 hover:file:bg-emerald-500/20 cursor-pointer"
                            >
                            <span class="text-[9px] text-gray-500 mt-1 block">تظهر هذه الصورة تلقائياً لأي منتج لا تتوفر له صورة خاصة في المتجر وشاشة البيع.</span>
                            <x-input-error :messages="$errors->get('default_product_image')" class="text-xs text-rose-500 mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Store Name -->
                <div class="space-y-1">
                    <label for="store_name" class="block text-xs font-semibold text-gray-300">اسم المؤسسة / المعرض <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="store_name" 
                        id="store_name" 
                        required 
                        placeholder="مثال: 2M Mobile"
                        value="{{ old('store_name', $settings['store_name'] ?? '2M Mobile') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('store_name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div class="space-y-1">
                        <label for="store_phone" class="block text-xs font-semibold text-gray-300">رقم الهاتف للاتصال</label>
                        <input 
                            type="text" 
                            name="store_phone" 
                            id="store_phone" 
                            placeholder="مثال: 01000000000"
                            value="{{ old('store_phone', $settings['store_phone'] ?? '01000000000') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]"
                        >
                        <x-input-error :messages="$errors->get('store_phone')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Tax Percentage -->
                    <div class="space-y-1">
                        <label for="tax_percentage" class="block text-xs font-semibold text-gray-300">نسبة ضريبة القيمة المضافة (%) <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            step="0.01"
                            name="tax_percentage" 
                            id="tax_percentage" 
                            required 
                            placeholder="14.00"
                            value="{{ old('tax_percentage', $settings['tax_percentage'] ?? '14.00') }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-[#D41414]"
                        >
                        <x-input-error :messages="$errors->get('tax_percentage')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>

                <!-- Customization Options -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-4">
                    <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">خيارات التخصيص والمظهر</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Theme Toggle -->
                        <div class="space-y-1">
                            <label for="theme_color" class="block text-[10px] text-gray-400">ثيم الموقع الافتراضي</label>
                            <select 
                                name="theme_color" 
                                id="theme_color" 
                                required 
                                class="block w-full px-2.5 py-1.5 bg-[#050505] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                                <option value="dark" {{ (old('theme_color', $settings['theme_color'] ?? 'dark') == 'dark') ? 'selected' : '' }}>اللون الأسود (Dark Mode)</option>
                                <option value="light" {{ (old('theme_color', $settings['theme_color'] ?? 'dark') == 'light') ? 'selected' : '' }}>اللون الأبيض (Light Mode)</option>
                            </select>
                            <x-input-error :messages="$errors->get('theme_color')" class="text-xs text-rose-500 mt-1" />
                        </div>

                        <!-- Language -->
                        <div class="space-y-1">
                            <label for="default_language" class="block text-[10px] text-gray-400">اللغة الافتراضية للنظام</label>
                            <select 
                                name="default_language" 
                                id="default_language" 
                                required 
                                class="block w-full px-2.5 py-1.5 bg-[#050505] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                                <option value="ar" {{ (old('default_language', $settings['default_language'] ?? 'ar') == 'ar') ? 'selected' : '' }}>اللغة العربية (Arabic)</option>
                                <option value="en" {{ (old('default_language', $settings['default_language'] ?? 'ar') == 'en') ? 'selected' : '' }}>اللغة الإنجليزية (English)</option>
                            </select>
                            <x-input-error :messages="$errors->get('default_language')" class="text-xs text-rose-500 mt-1" />
                        </div>

                        <!-- Currency -->
                        <div class="space-y-1">
                            <label for="default_currency" class="block text-[10px] text-gray-400">العملة الافتراضية</label>
                            <input 
                                type="text" 
                                name="default_currency" 
                                id="default_currency" 
                                required 
                                placeholder="ج.م"
                                value="{{ old('default_currency', $settings['default_currency'] ?? 'ج.م') }}"
                                class="block w-full px-2.5 py-1.5 bg-[#050505] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                            <x-input-error :messages="$errors->get('default_currency')" class="text-xs text-rose-500 mt-1" />
                        </div>
                    </div>
                </div>

                <!-- POS & Cashier Automation Settings Card -->
                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-white/5 pb-2">
                        <h3 class="text-xs font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-cash-register text-[#D41414]"></i>
                            <span>إعدادات شاشة البيع والكاشير والطباعة التلقائية</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Auto-Checkout on Barcode Scan -->
                        <div class="space-y-1.5">
                            <label for="auto_checkout_on_barcode" class="block text-xs font-semibold text-gray-300">
                                <i class="fa-solid fa-barcode text-amber-400 ml-1"></i> غلق ودفع الفاتورة بعد مسح الباركود
                            </label>
                            <select 
                                name="auto_checkout_on_barcode" 
                                id="auto_checkout_on_barcode" 
                                class="block w-full px-3 py-2 bg-[#050505] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                                <option value="0" {{ (old('auto_checkout_on_barcode', $settings['auto_checkout_on_barcode'] ?? '0') == '0') ? 'selected' : '' }}>
                                    🔴 معطل (الافتراضي: إضافة المنتج للسلة فقط وانتظار الضغط على دفع)
                                </option>
                                <option value="1" {{ (old('auto_checkout_on_barcode', $settings['auto_checkout_on_barcode'] ?? '0') == '1') ? 'selected' : '' }}>
                                    🟢 مفعل (غلق ودفع الفاتورة فوراً بمجرد قراءة الباركود بالاسكانر)
                                </option>
                            </select>
                            <p class="text-[10px] text-gray-400">عند تفعيلها، ينهي النظام الفاتورة بمجرد مسح الباركود دون الحاجة للضغط على زر الدفع.</p>
                            <x-input-error :messages="$errors->get('auto_checkout_on_barcode')" class="text-xs text-rose-500 mt-1" />
                        </div>

                        <!-- Auto-Print Receipt Setting -->
                        <div class="space-y-1.5">
                            <label for="auto_print_receipt" class="block text-xs font-semibold text-gray-300">
                                <i class="fa-solid fa-print text-[#D41414] ml-1"></i> الطباعة التلقائية للفاتورة بعد البيع
                            </label>
                            <select 
                                name="auto_print_receipt" 
                                id="auto_print_receipt" 
                                required 
                                class="block w-full px-3 py-2 bg-[#050505] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                            >
                                <option value="0" {{ (old('auto_print_receipt', $settings['auto_print_receipt'] ?? '0') == '0') ? 'selected' : '' }}>
                                    🔴 معطل (الافتراضي: لا يطبع الفاتورة تلقائياً إلا لو طلب الكاشير)
                                </option>
                                <option value="1" {{ (old('auto_print_receipt', $settings['auto_print_receipt'] ?? '0') == '1') ? 'selected' : '' }}>
                                    🟢 مفعل (فتح وطباعة الفاتورة تلقائياً مع كل عملية بيع)
                                </option>
                            </select>
                            <p class="text-[10px] text-gray-400">يمكنك تحديد ما إذا كان النظام يطبع الفاتورة تلقائياً بعد كل بيع أو يكتفي بحفظها.</p>
                            <x-input-error :messages="$errors->get('auto_print_receipt')" class="text-xs text-rose-500 mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="store_address" class="block text-xs font-semibold text-gray-300">العنوان الرئيسي للمؤسسة</label>
                    <input 
                        type="text" 
                        name="store_address" 
                        id="store_address" 
                        placeholder="مثال: شارع التحرير، القاهرة"
                        value="{{ old('store_address', $settings['store_address'] ?? 'شارع التحرير، القاهرة') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                    <x-input-error :messages="$errors->get('store_address')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Receipt Footer text -->
                <div class="space-y-1">
                    <label for="receipt_footer" class="block text-xs font-semibold text-gray-300">نص تذييل الفاتورة الحرارية</label>
                    <textarea 
                        name="receipt_footer" 
                        id="receipt_footer" 
                        rows="2" 
                        placeholder="النص الذي يظهر في أسفل إيصال المبيعات المطبوع..."
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >{{ old('receipt_footer', $settings['receipt_footer'] ?? 'لا يتم الاسترجاع أو الاستبدال بدون الفاتورة.') }}</textarea>
                    <x-input-error :messages="$errors->get('receipt_footer')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Buttons -->
                <div class="pt-4 border-t border-white/5 flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                        حفظ الإعدادات والتخصيص <i class="fa-solid fa-floppy-disk mr-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Branches Configuration Summary (1 Col) -->
        <div class="space-y-6">
            
            <!-- Branches Stats Card -->
            <div class="glass-panel p-5 flex flex-col justify-between space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] text-gray-500 block">الفروع النشطة بالنظام</span>
                        <h4 class="text-lg font-bold text-white mt-1">توزيع الفروع الحالية</h4>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                        <i class="fa-solid fa-code-branch"></i>
                    </div>
                </div>

                <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-gray-400 text-xs">عدد الفروع المضافة:</span>
                        <span class="text-xl font-black text-white font-mono block mt-1">{{ $branchesCount }} فروع</span>
                    </div>
                    <a href="{{ route('branches.index') }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-lg text-[10px] font-bold transition">
                        إدارة الفروع <i class="fa-solid fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </div>

            <!-- List of branches -->
            <div class="glass-panel p-5 space-y-3">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">قائمة الفروع المسجلة</h3>
                <div class="space-y-2">
                    @forelse($branches as $b)
                        <div class="flex justify-between items-center text-xs p-2 bg-[#0a0a0a] border border-white/5 rounded-lg">
                            <span class="text-white font-semibold">{{ $b->name }}</span>
                            <span class="text-gray-500 text-[10px] font-mono">{{ $b->phone }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-2">لا توجد فروع مسجلة حالياً.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>