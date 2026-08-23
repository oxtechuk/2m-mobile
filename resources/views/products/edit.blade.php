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
                    <label for="sku" class="block text-xs font-semibold text-gray-300">رمز المنتج (SKU) <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="sku" 
                        id="sku" 
                        required 
                        value="{{ old('sku', $product->sku) }}"
                        class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition text-xs font-mono text-left"
                    >
                    <x-input-error :messages="$errors->get('sku')" class="text-xs text-rose-500 mt-1" />
                </div>

                <!-- Barcode -->
                <div class="space-y-1">
                    <label for="barcode" class="block text-xs font-semibold text-gray-300">رقم الباركود</label>
                    <input 
                        type="text" 
                        name="barcode" 
                        id="barcode" 
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
            <div class="p-4 bg-teal-950/20 border border-teal-500/30 rounded-xl space-y-3">
                <div class="flex items-center gap-2 text-teal-400 font-bold text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>تعديل رصيد المخزن الحالي (Current Stock):</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Current Stock Quantity -->
                    <div class="space-y-1">
                        <label for="current_stock" class="block text-xs font-semibold text-gray-200">الكمية الحالية في المخزن</label>
                        <input 
                            type="number" 
                            name="current_stock" 
                            id="current_stock" 
                            min="0"
                            step="1"
                            value="{{ old('current_stock', $currentStock) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-lg text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition text-xs font-mono font-bold text-left"
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

            <!-- Has Serials Toggle -->
            <div class="flex items-center p-3 bg-[#0a0a0a] rounded-lg border border-white/5">
                <input 
                    type="checkbox" 
                    name="has_serials" 
                    id="has_serials" 
                    value="1" 
                    {{ old('has_serials', $product->has_serials) ? 'checked' : '' }}
                    class="rounded bg-[#0a0a0a] border-white/10 text-[#D41414] focus:ring-[#D41414]"
                >
                <label for="has_serials" class="mr-2 text-xs font-semibold text-gray-300 select-none">
                    يتطلب أرقام تسلسلية فريدة (IMEI / Serials) مثل الهواتف والتابلت
                </label>
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