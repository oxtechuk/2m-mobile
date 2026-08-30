<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-plus ml-2 text-[#D41414]"></i>إضافة منتج جديد</h2>
            <p class="text-xs text-gray-500 mt-1">تسجيل جهاز ذكي أو قطعة غيار أو إكسسوار وتحديد رصيد المخزن الأولي.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('products.store') }}" class="space-y-4" x-data="{
            hasSerials: {{ old('has_serials') ? 'true' : 'false' }},
            stockQty: {{ old('opening_stock', 0) }},
            serials: [''],
            init() {
                this.syncSerials();
            },
            syncSerials() {
                const target = Math.max(1, parseInt(this.stockQty) || 1);
                while (this.serials.length < target) {
                    this.serials.push('');
                }
            },
            addSerial() {
                this.serials.push('');
            },
            removeSerial(idx) {
                if (this.serials.length > 1) {
                    this.serials.splice(idx, 1);
                } else {
                    this.serials[0] = '';
                }
            }
        }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-1 sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم المنتج بالكامل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="مثال: iPhone 15 Pro Max 256GB Black"
                        value="{{ old('name') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                    <x-input-error :messages="$errors->get('name')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- SKU -->
                <div class="space-y-1">
                    <label for="sku" class="block text-xs font-semibold text-gray-300">رمز المنتج (SKU) <span class="text-xs text-gray-500 font-normal">(اختياري)</span></label>
                    <input 
                        type="text" 
                        name="sku" 
                        id="sku" 
                        placeholder="اختياري: مثال IPH15PM-256"
                        value="{{ old('sku') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('sku')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Barcode -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="barcode" class="block text-xs font-semibold text-gray-300">رقم الباركود <span class="text-rose-500">*</span></label>
                        <button 
                            type="button" 
                            onclick="document.getElementById('barcode').value = '200' + Math.floor(Math.random() * 89999999 + 10000000);"
                            class="text-[10px] text-teal-400 hover:underline font-mono"
                        >
                            توليد تلقائي
                        </button>
                    </div>
                    <input 
                        type="text" 
                        name="barcode" 
                        id="barcode" 
                        required
                        placeholder="رقم الباركود الدولي أو التلقائي"
                        value="{{ old('barcode') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('barcode')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Category -->
                <div class="space-y-1">
                    <label for="category_id" class="block text-xs font-semibold text-gray-300">التصنيف <span class="text-rose-500">*</span></label>
                    <select 
                        name="category_id" 
                        id="category_id" 
                        required 
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Unit -->
                <div class="space-y-1">
                    <label for="unit" class="block text-xs font-semibold text-gray-300">الوحدة <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="unit" 
                        id="unit" 
                        required 
                        placeholder="مثال: قطعة، علبة"
                        value="{{ old('unit', 'قطعة') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                    >
                    <x-input-error :messages="$errors->get('unit')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Cost Price -->
                <div class="space-y-1">
                    <label for="cost_price" class="block text-xs font-semibold text-gray-300">سعر تكلفة الشراء <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0"
                        name="cost_price" 
                        id="cost_price" 
                        required 
                        placeholder="0.00"
                        value="{{ old('cost_price') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('cost_price')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Selling Price -->
                <div class="space-y-1">
                    <label for="selling_price" class="block text-xs font-semibold text-gray-300">سعر البيع (قطاعي) <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0"
                        name="selling_price" 
                        id="selling_price" 
                        required 
                        placeholder="0.00"
                        value="{{ old('selling_price') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('selling_price')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Wholesale Price -->
                <div class="space-y-1">
                    <label for="wholesale_price" class="block text-xs font-semibold text-gray-300">سعر بيع الجملة (للفنيين)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0"
                        name="wholesale_price" 
                        id="wholesale_price" 
                        placeholder="اختياري"
                        value="{{ old('wholesale_price') }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('wholesale_price')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Minimum Stock Limit -->
                <div class="space-y-1">
                    <label for="minimum_stock" class="block text-xs font-semibold text-gray-300">الحد الأدنى للتنبيه بالمخزن <span class="text-rose-500">*</span></label>
                    <input 
                        type="number" 
                        name="minimum_stock" 
                        id="minimum_stock" 
                        required 
                        placeholder="5"
                        value="{{ old('minimum_stock', 5) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('minimum_stock')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- Initial / Opening Stock Section -->
            <div class="p-4 bg-[#0f2e2e] border border-teal-500/40 rounded-xl space-y-3 shadow-inner">
                <div class="flex items-center gap-2 text-teal-300 font-bold text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>الرصيد الافتتاحي بالمخزن (Initial Stock):</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Opening Stock Quantity -->
                    <div class="space-y-1">
                        <label for="opening_stock" class="block text-xs font-semibold text-teal-100">الكمية الأولية المتوفرة حالياً <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            name="opening_stock" 
                            id="opening_stock" 
                            min="0"
                            step="1"
                            required
                            placeholder="0"
                            x-model.number="stockQty"
                            @input="syncSerials()"
                            value="{{ old('opening_stock', 0) }}"
                            class="block w-full px-3 py-2 bg-[#081a1a] border border-teal-500/30 rounded-lg text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-xs font-mono font-bold text-left"
                        >
                        <x-input-error :messages="$errors->get('opening_stock')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Target Branch -->
                    <div class="space-y-1">
                        <label for="branch_id" class="block text-xs font-semibold text-teal-100">الفرع المودع به الرصيد <span class="text-rose-500">*</span></label>
                        <select 
                            name="branch_id" 
                            id="branch_id" 
                            required
                            class="block w-full px-3 py-2 bg-[#081a1a] border border-teal-500/30 rounded-lg text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-xs"
                        >
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ (old('branch_id', auth()->user()->branch_id) == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('branch_id')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- Has Serials Toggle & Dynamic Serials Input List -->
            <div class="space-y-3 p-4 bg-[#0a0a0a] rounded-xl border border-white/10">
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="has_serials" 
                        id="has_serials" 
                        value="1" 
                        x-model="hasSerials"
                        class="rounded bg-[#0a0a0a] border-white/10 text-[#D41414] focus:ring-[#D41414] w-4 h-4 cursor-pointer"
                    >
                    <label for="has_serials" class="mr-2.5 text-xs font-bold text-gray-200 select-none flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-barcode text-teal-400 text-sm"></i>
                        <span>يتطلب أرقام تسلسلية فريدة (IMEI / Serials) مثل الهواتف والتابلت</span>
                    </label>
                </div>

                <!-- Dynamic Serials Section -->
                <div x-show="hasSerials" x-collapse class="pt-3 border-t border-white/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-teal-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            <span>إدخال أرقام السيريال (IMEI) لكل قطعة متوفرة بالمخزن:</span>
                        </span>
                        <button 
                            type="button" 
                            @click="addSerial()" 
                            class="px-2.5 py-1 bg-teal-500/15 border border-teal-500/30 text-teal-300 hover:bg-teal-500/25 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer"
                        >
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>إضافة سيريال جديد</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <template x-for="(s, index) in serials" :key="index">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-gray-400">
                                    سيريال (IMEI) القطعة #<span x-text="index + 1"></span>
                                </label>
                                <div class="flex items-center gap-1">
                                    <input 
                                        type="text" 
                                        name="serials[]" 
                                        x-model="serials[index]"
                                        placeholder="امسح بالباركود أو اكتب السيريال"
                                        class="block w-full px-3 py-1.5 bg-black/40 border border-white/15 rounded-lg text-white font-mono text-xs text-left focus:outline-none focus:border-teal-400"
                                    >
                                    <button 
                                        type="button" 
                                        @click="removeSerial(index)" 
                                        title="حذف السيريال"
                                        class="px-2 py-1.5 text-gray-500 hover:text-rose-400 text-xs transition cursor-pointer"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p class="text-[10px] text-gray-400 bg-teal-500/10 p-2 rounded-lg border border-teal-500/20">
                        💡 يمكنك استخدام قارئ الباركود (Barcode Scanner) لمسح أرقام السيريال نمبر مباشرة داخل الخانات.
                    </p>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label for="description" class="block text-xs font-semibold text-gray-300">وصف المنتج</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="2" 
                    placeholder="مواصفات إضافية"
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white placeholder-gray-600 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    حفظ المنتج وتسجيل المخزون <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>