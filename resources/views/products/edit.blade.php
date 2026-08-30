<x-app-layout>
    <div class="max-w-2xl mx-auto glass-panel p-6 space-y-6">
        
        <!-- Header -->
        <div class="border-b border-white/5 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-white"><i class="fa-solid fa-pen-to-square ml-2 text-[#D41414]"></i>تعديل بيانات المنتج</h2>
                <p class="text-xs text-gray-500 mt-1">تحديث تفاصيل المنتج وأسعاره ورصيد المخزن المسجل.</p>
            </div>
            <!-- Delete Form -->
            <form method="POST" action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج نهائياً؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center">
                    <i class="fa-solid fa-trash ml-1"></i> حذف المنتج
                </button>
            </form>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="space-y-1 sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-300">اسم المنتج بالكامل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        value="{{ old('name', $product->name) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
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
                        placeholder="اختياري: رمز المنتج الكودي"
                        value="{{ old('sku', $product->sku) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('sku')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Barcode -->
                <div class="space-y-1">
                    <label for="barcode" class="block text-xs font-semibold text-gray-300">رقم الباركود <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="barcode" 
                        id="barcode" 
                        required
                        placeholder="رقم الباركود الدولي أو التلقائي"
                        value="{{ old('barcode', $product->barcode) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
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
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                        value="{{ old('unit', $product->unit) }}"
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
                        value="{{ old('cost_price', $product->cost_price) }}"
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
                        value="{{ old('selling_price', $product->selling_price) }}"
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
                        value="{{ old('wholesale_price', $product->wholesale_price) }}"
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
                        value="{{ old('minimum_stock', $product->minimum_stock) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('minimum_stock')" class="text-xs text-rose-500 mt-1" />
                </div>
            </div>

            <!-- Current Stock Adjustment Section -->
            <div class="p-4 bg-[#0f2e2e] border border-teal-500/40 rounded-xl space-y-3 shadow-inner">
                <div class="flex items-center gap-2 text-teal-300 font-bold text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>تعديل رصيد المخزن الحالي (Current Stock):</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Current Stock Quantity -->
                    <div class="space-y-1">
                        <label for="current_stock" class="block text-xs font-semibold text-teal-100">الكمية الحالية في المخزن</label>
                        <input 
                            type="number" 
                            name="current_stock" 
                            id="current_stock" 
                            min="0"
                            step="1"
                            value="{{ old('current_stock', $currentStock) }}"
                            class="block w-full px-3 py-2 bg-[#081a1a] border border-teal-500/30 rounded-lg text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-xs font-mono font-bold text-left"
                        >
                        <x-input-error :messages="$errors->get('current_stock')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Target Branch -->
                    <div class="space-y-1">
                        <label for="branch_id" class="block text-xs font-semibold text-gray-200">الفرع المودع به</label>
                        <select 
                            name="branch_id" 
                            id="branch_id" 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-xs"
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
            <div class="space-y-3 p-4 bg-[#0a0a0a] rounded-xl border border-white/10" x-data="{
                hasSerials: {{ old('has_serials', $product->has_serials) ? 'true' : 'false' }},
                newSerials: [''],
                addNewSerial() {
                    this.newSerials.push('');
                },
                removeNewSerial(idx) {
                    if (this.newSerials.length > 1) {
                        this.newSerials.splice(idx, 1);
                    } else {
                        this.newSerials[0] = '';
                    }
                }
            }">
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
                <div x-show="hasSerials" x-collapse class="pt-3 border-t border-white/10 space-y-4">
                    
                    <!-- Existing Serials in Stock -->
                    @php
                        $inStockSerials = $product->serials ? $product->serials->where('status', 'in_stock') : collect();
                    @endphp
                    @if($inStockSerials->count() > 0)
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-gray-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-boxes-stacked text-teal-400"></i>
                                <span>السيريالات المسجلة حالياً بالمخزن ({{ $inStockSerials->count() }} قطعة):</span>
                            </span>

                            <div class="flex flex-wrap gap-2">
                                @foreach($inStockSerials as $s)
                                    <label class="flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-xs font-mono text-gray-200 hover:border-rose-500/50 cursor-pointer" title="حدد لحذف السيريال">
                                        <input type="checkbox" name="remove_serials[]" value="{{ $s->id }}" class="rounded bg-black border-white/20 text-rose-500 focus:ring-rose-500">
                                        <span>{{ $s->serial_number }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-gray-500">ضع علامة صح ❌ على السيريال في حالة رغبتك في حذفه من المخزن عند التحديث.</p>
                        </div>
                    @endif

                    <!-- Add New Serials -->
                    <div class="space-y-3 pt-2 border-t border-white/10">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>إضافة أرقام سيريال جديدة (IMEI):</span>
                            </span>
                            <button 
                                type="button" 
                                @click="addNewSerial()" 
                                class="px-2.5 py-1 bg-teal-500/15 border border-teal-500/30 text-teal-300 hover:bg-teal-500/25 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer"
                            >
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>إضافة خانة سيريال</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <template x-for="(s, index) in newSerials" :key="index">
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-gray-400">
                                        سيريال جديد #<span x-text="index + 1"></span>
                                    </label>
                                    <div class="flex items-center gap-1">
                                        <input 
                                            type="text" 
                                            name="serials[]" 
                                            x-model="newSerials[index]"
                                            placeholder="امسح بالباركود أو اكتب IMEI"
                                            class="block w-full px-3 py-1.5 bg-black/40 border border-white/15 rounded-lg text-white font-mono text-xs text-left focus:outline-none focus:border-teal-400"
                                        >
                                        <button 
                                            type="button" 
                                            @click="removeNewSerial(index)" 
                                            title="حذف"
                                            class="px-2 py-1.5 text-gray-500 hover:text-rose-400 text-xs transition cursor-pointer"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label for="description" class="block text-xs font-semibold text-gray-300">وصف المنتج</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="2" 
                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs"
                >{{ old('description', $product->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="text-xs text-rose-500 mt-1" />
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-white/5 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-lg text-xs font-bold transition shadow-lg glow-primary">
                    تحديث المنتج ورصيد المخزن <i class="fa-solid fa-floppy-disk mr-1"></i>
                </button>
            </div>

        </form>

    </div>
</x-app-layout>