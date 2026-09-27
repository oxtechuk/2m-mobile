<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6" x-data="purchaseForm()">
        
        <!-- Header -->
        <div class="flex items-center justify-between glass-panel p-4 md:p-6">
            <div>
                <h1 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-cart-plus text-indigo-400"></i>
                    <span>إنشاء فاتورة توريد / شراء جديدة</span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">تعديل التكلفة وإضافة كميات المنتجات ورصيد المخزون وحساب المستحقات للمورد.</p>
            </div>

            <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                سجل الفواتير <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-6">
            @csrf

            <!-- Basic Details Card -->
            <div class="glass-panel p-6 space-y-4">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-indigo-400"></i>
                    <span>البيانات الأساسية للفاتورة والجهة الموردة</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Supplier -->
                    <div class="space-y-1">
                        <label for="supplier_id" class="block text-xs font-semibold text-gray-300">اختيار المورد <span class="text-rose-500">*</span></label>
                        <select 
                            name="supplier_id" 
                            id="supplier_id" 
                            required 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                        >
                            <option value="">-- اختر المورد من الدليل --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id', request('supplier_id')) == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }} {{ $sup->company_name ? "({$sup->company_name})" : '' }} (رصيد مستحق: {{ number_format($sup->current_balance, 2) }} ج.م)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Branch -->
                    <div class="space-y-1">
                        <label for="branch_id" class="block text-xs font-semibold text-gray-300">الفرع المستلم للشحنة والمخزون <span class="text-rose-500">*</span></label>
                        <select 
                            name="branch_id" 
                            id="branch_id" 
                            required 
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                        >
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', auth()->user()->branch_id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('branch_id')" class="text-xs text-rose-500 mt-1" />
                    </div>

                    <!-- Date -->
                    <div class="space-y-1">
                        <label for="purchase_date" class="block text-xs font-semibold text-gray-300">تاريخ الفاتورة والتوريد <span class="text-rose-500">*</span></label>
                        <input 
                            type="datetime-local" 
                            name="purchase_date" 
                            id="purchase_date" 
                            required 
                            value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}"
                            class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white font-mono text-xs focus:outline-none focus:border-indigo-500"
                        >
                        <x-input-error :messages="$errors->get('purchase_date')" class="text-xs text-rose-500 mt-1" />
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="glass-panel p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                    <h3 class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-indigo-400"></i>
                        <span>قائمة الأجهزة والمنتجات الموردة</span>
                    </h3>

                    <button 
                        type="button" 
                        @click="addItem()" 
                        class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 rounded-lg text-xs font-bold transition flex items-center gap-1"
                    >
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>إضافة منتج آخر</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#0a0a0a] text-gray-400 border-b border-white/5 font-bold">
                            <tr>
                                <th class="p-3 w-5/12">المنتج / الصنف</th>
                                <th class="p-3 w-2/12">الكمية الواردة</th>
                                <th class="p-3 w-2/12">سعر تكلفة القطعة</th>
                                <th class="p-3 w-2/12">الإجمالي الفرعي</th>
                                <th class="p-3 w-1/12 text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-white/[0.01]">
                                    <td class="p-3">
                                        <select 
                                            :name="`items[${index}][product_id]`" 
                                            x-model="item.product_id" 
                                            @change="onProductSelect(index)"
                                            required 
                                            class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white text-xs focus:outline-none focus:border-indigo-500"
                                        >
                                            <option value="">-- اختر المنتج --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-cost="{{ $p->cost_price }}" data-serials="{{ $p->has_serials ? 1 : 0 }}">
                                                    {{ $p->name }} (سعر التكلفة الحالي: {{ number_format($p->cost_price, 2) }} ج.م)
                                                </option>
                                            @endforeach
                                        </select>

                                        <!-- IMEIs Textarea if product requires serials -->
                                        <div x-show="item.has_serials" class="mt-2">
                                            <label class="block text-[10px] text-amber-400 font-bold mb-1">
                                                <i class="fa-solid fa-barcode mr-1"></i> الأرقام التسلسلية / S/N أو IMEI (اكتب كل سيريال في سطر مستقل):
                                            </label>
                                            <textarea 
                                                :name="`items[${index}][serials]`" 
                                                x-model="item.serials" 
                                                rows="2" 
                                                placeholder="أدخل الأرقام التسلسلية..." 
                                                class="block w-full px-2 py-1 bg-[#050505] border border-amber-500/30 rounded-lg text-amber-300 font-mono text-[10px] focus:outline-none focus:border-amber-400"
                                            ></textarea>
                                        </div>
                                    </td>

                                    <td class="p-3">
                                        <input 
                                            type="number" 
                                            min="1" 
                                            :name="`items[${index}][quantity]`" 
                                            x-model.number="item.quantity" 
                                            required 
                                            class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-center text-xs focus:outline-none focus:border-indigo-500"
                                        >
                                    </td>

                                    <td class="p-3">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0" 
                                            :name="`items[${index}][unit_cost]`" 
                                            x-model.number="item.unit_cost" 
                                            required 
                                            class="block w-full px-2.5 py-1.5 bg-[#0a0a0a] border border-white/10 rounded-lg text-white font-mono text-left text-xs focus:outline-none focus:border-indigo-500"
                                        >
                                    </td>

                                    <td class="p-3 font-mono font-bold text-white text-sm">
                                        <span x-text="formatCurrency(item.quantity * item.unit_cost)"></span> ج.م
                                    </td>

                                    <td class="p-3 text-center">
                                        <button 
                                            type="button" 
                                            @click="removeItem(index)" 
                                            :disabled="items.length <= 1"
                                            class="p-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded-lg text-xs transition disabled:opacity-30 cursor-pointer"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total & Payment Card -->
            <div class="glass-panel p-6 space-y-4">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-indigo-400"></i>
                    <span>حساب الفاتورة وإجراءات السداد الدفعة المالية</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Invoice Summary Panel -->
                    <div class="bg-[#0a0a0a] border border-white/5 p-4 rounded-xl space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-400">إجمالي قيمة الشحنة:</span>
                            <span class="font-mono font-black text-lg text-white"><span x-text="formatCurrency(grandTotal)"></span> ج.م</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-white/5 pt-2">
                            <span class="text-gray-400">المبلغ المدفوع فوراً:</span>
                            <span class="font-mono font-bold text-emerald-400"><span x-text="formatCurrency(paidAmount)"></span> ج.م</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-white/5 pt-2">
                            <span class="text-gray-400">المتبقي آجل للمورد:</span>
                            <span class="font-mono font-bold text-rose-400"><span x-text="formatCurrency(dueAmount)"></span> ج.م</span>
                        </div>
                    </div>

                    <!-- Payment Inputs -->
                    <div class="space-y-3 md:col-span-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Paid Amount -->
                            <div class="space-y-1">
                                <label for="paid_amount" class="block text-xs font-semibold text-gray-300">المبلغ المدفوع فوراً (ج.م)</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    name="paid_amount" 
                                    id="paid_amount" 
                                    x-model.number="paidAmount" 
                                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-emerald-400 font-mono text-left font-bold text-sm focus:outline-none focus:border-indigo-500"
                                >
                            </div>

                            <!-- Payment Method -->
                            <div class="space-y-1">
                                <label for="payment_method" class="block text-xs font-semibold text-gray-300">طريقة الدفع</label>
                                <select 
                                    name="payment_method" 
                                    id="payment_method" 
                                    class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                                >
                                    <option value="cash">نقداً (كاش)</option>
                                    <option value="bank_transfer">تحويل بنكي / Vodafone Cash</option>
                                    <option value="cheque">شيك</option>
                                </select>
                            </div>
                        </div>

                        <!-- Wallet Selection (Required if paidAmount > 0) -->
                        <div x-show="paidAmount > 0" class="space-y-1">
                            <label for="wallet_id" class="block text-xs font-semibold text-gray-300">الخزينة / المحفظة المخصوم منها المبلغ <span class="text-rose-500">*</span></label>
                            <select 
                                name="wallet_id" 
                                id="wallet_id" 
                                class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                            >
                                @foreach($wallets as $w)
                                    <option value="{{ $w->id }}">
                                        {{ $w->name }} (الرصيد المتاح: {{ number_format($w->balance, 2) }} ج.م)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label for="notes" class="block text-xs font-semibold text-gray-300">ملاحظات الفاتورة</label>
                            <input 
                                type="text" 
                                name="notes" 
                                id="notes" 
                                placeholder="أي ملاحظات حول الشحنة، رقم الشحن، أو الضمان..." 
                                class="block w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-500"
                            >
                        </div>
                    </div>
                </div>

                <!-- Submit Area -->
                <div class="pt-4 border-t border-white/5 flex justify-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-xl text-xs font-bold transition shadow-lg glow-primary">
                        حفظ الفاتورة وتوريد البضاعة للمخزن <i class="fa-solid fa-check-double mr-1"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script>
        function purchaseForm() {
            return {
                items: [
                    { product_id: '', quantity: 1, unit_cost: 0, has_serials: false, serials: '' }
                ],
                paidAmount: 0,

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0, has_serials: false, serials: '' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                onProductSelect(index) {
                    const select = document.querySelectorAll('select[name^="items"]')[index];
                    const selectedOpt = select.options[select.selectedIndex];
                    if (selectedOpt && selectedOpt.value) {
                        this.items[index].unit_cost = parseFloat(selectedOpt.dataset.cost) || 0;
                        this.items[index].has_serials = selectedOpt.dataset.serials === "1";
                    }
                },

                get grandTotal() {
                    return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_cost || 0)), 0);
                },

                get dueAmount() {
                    return Math.max(0, this.grandTotal - (this.paidAmount || 0));
                },

                formatCurrency(val) {
                    return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
                }
            }
        }
    </script>
</x-app-layout>
