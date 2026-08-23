<x-app-layout>
    @php
        $categories = \App\Models\Category::withCount('products')->get();
        $products = \App\Models\Product::with('category')->where('is_active', true)->get();
        $customers = \App\Models\Customer::all();
        $currentBranch = auth()->user()->branch->name ?? 'الفرع الرئيسي';
    @endphp

    <style>
        .pos-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            height: calc(100vh - 6.5rem);
            width: 100%;
            overflow: hidden;
        }
        .pos-grid {
            display: flex;
            flex-direction: row;
            gap: 0.75rem;
            flex: 1;
            min-height: 0;
            min-width: 0;
            width: 100%;
        }
        .pos-products-panel {
            flex: 1 1 0%;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .pos-cart-panel {
            width: 380px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 767px) {
            .pos-grid {
                flex-direction: column;
            }
            .pos-cart-panel {
                width: 100%;
            }
        }
    </style>

    <script>
        window.defaultCurrency = "{{ setting('default_currency', 'ج.م') }}";
        window.posProducts = @json($products);

        function posApp(initialProducts) {
            return {
                products: initialProducts || [],
                customerList: @json($customers),
                searchQuery: '',
                selectedCategory: '',
                cart: [],
                selectedCustomer: '',
                autoCheckoutOnBarcode: {{ setting('auto_checkout_on_barcode', '0') == '1' ? 'true' : 'false' }},
                printReceiptOnCheckout: {{ setting('auto_print_receipt', '0') == '1' ? 'true' : 'false' }},
                showCartOnMobile: false,
                showQuickCustomerModal: false,
                isSavingCustomer: false,
                isCheckingOut: false,
                newCustomer: {
                    name: '',
                    phone: '',
                    address: '',
                    notes: ''
                },
                openQuickCustomerModal() {
                    this.newCustomer = { name: '', phone: '', address: '', notes: '' };
                    this.showQuickCustomerModal = true;
                    this.$nextTick(() => {
                        if (this.$refs.quickCustName) this.$refs.quickCustName.focus();
                    });
                },
                async saveQuickCustomer() {
                    if (!this.newCustomer.name.trim() || !this.newCustomer.phone.trim()) {
                        alert('يرجى كتابة اسم ورقم هاتف العميل.');
                        return;
                    }
                    this.isSavingCustomer = true;
                    try {
                        const res = await fetch("{{ route('customers.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.newCustomer)
                        });
                        const data = await res.json();
                        if (data.success && data.customer) {
                            this.customerList.unshift(data.customer);
                            this.selectedCustomer = data.customer.id;
                            this.showQuickCustomerModal = false;
                            this.focusSearch();
                        } else {
                            alert(data.message || 'حدث خطأ أثناء حفظ العميل.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('تعذر حفظ العميل، يرجى التأكد من صحة البيانات وعدم تكرار رقم الهاتف.');
                    } finally {
                        this.isSavingCustomer = false;
                    }
                },
                init() {
                    this.focusSearch();
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F8') {
                            e.preventDefault();
                            this.checkout(false);
                        } else if (e.key === 'F9') {
                            e.preventDefault();
                            this.checkout(true);
                        }
                    });
                },
                focusSearch() {
                    this.$nextTick(() => {
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.focus();
                            this.$refs.searchInput.select();
                        }
                    });
                },
                get filteredProducts() {
                    return this.products.filter(prod => {
                        const matchesCategory = !this.selectedCategory || (prod.category_id == this.selectedCategory);
                        const query = this.searchQuery.toLowerCase().trim();
                        const matchesSearch = !query || 
                            (prod.name && prod.name.toLowerCase().includes(query)) ||
                            (prod.barcode && prod.barcode.toLowerCase().includes(query)) ||
                            (prod.sku && prod.sku.toLowerCase().includes(query));
                        return matchesCategory && matchesSearch;
                    });
                },
                addToCart(prod) {
                    if (!prod) return;
                    let found = this.cart.find(item => item.id === prod.id);
                    if (found) {
                        found.qty++;
                    } else {
                        this.cart.push({
                            id: prod.id,
                            name: prod.name,
                            selling_price: parseFloat(prod.selling_price),
                            qty: 1
                        });
                    }
                    this.searchQuery = '';
                    this.focusSearch();
                },
                handleEnterKey() {
                    const list = this.filteredProducts;
                    if (list.length > 0) {
                        this.addToCart(list[0]);
                        // If Auto-Checkout on Barcode is enabled in settings
                        if (this.autoCheckoutOnBarcode && this.cart.length > 0) {
                            this.$nextTick(() => {
                                this.checkout();
                            });
                        }
                    } else {
                        this.searchQuery = '';
                        this.focusSearch();
                    }
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.focusSearch();
                },
                increaseQty(index) {
                    this.cart[index].qty++;
                },
                decreaseQty(index) {
                    if (this.cart[index].qty > 1) {
                        this.cart[index].qty--;
                    } else {
                        this.removeFromCart(index);
                    }
                },
                clearCart() {
                    if (this.cart.length === 0) return;
                    if (confirm('هل أنت تأكد من تفريغ سلة المشتريات بالكامل؟')) {
                        this.cart = [];
                        this.focusSearch();
                    }
                },
                cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },
                subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.selling_price * item.qty), 0);
                },
                tax() {
                    return this.subtotal() * 0.14;
                },
                total() {
                    return this.subtotal() + this.tax();
                },
                numberFormat(val) {
                    return (val || 0).toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                async checkout(overridePrint = null) {
                    if (this.cart.length === 0 || this.isCheckingOut) return;

                    const shouldPrint = (overridePrint !== null) ? overridePrint : this.printReceiptOnCheckout;
                    this.isCheckingOut = true;

                    try {
                        const response = await fetch("{{ route('pos.sale') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                customer_id: this.selectedCustomer || null,
                                cart: this.cart,
                                payment_method: 'cash',
                                discount: 0
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            alert('✅ تم تسجيل وتأكيد عملية البيع بنجاح!\nرقم الفاتورة: #' + data.invoice);
                            
                            // Only open/print invoice if specifically enabled by setting or button
                            if (shouldPrint && data.invoice_url) {
                                window.open(data.invoice_url, '_blank');
                            }
                            
                            this.cart = [];
                            this.selectedCustomer = '';
                            this.showCartOnMobile = false;
                            this.focusSearch();
                        } else {
                            alert(data.message || 'حدث خطأ أثناء حفظ عملية البيع.');
                        }
                    } catch(err) {
                        alert('حدث خطأ بالاتصال مع السيرفر: ' + err.message);
                    } finally {
                        this.isCheckingOut = false;
                    }
                }
            }
        }
    </script>

    <!-- POS Root Container -->
    <div class="pos-wrapper" x-data="posApp(window.posProducts)">
        
        <!-- Header Bar -->
        <div class="glass-panel px-4 py-2.5 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#D41414]/10 text-[#D41414] flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-tight">شاشة البيع السريع (POS)</h1>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">فرع التشغيل: <strong class="text-gray-900 dark:text-white">{{ $currentBranch }}</strong></p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <!-- Status Badge -->
                <span class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>الماسح الضوئي نشط (F8 للإنهاء)</span>
                </span>

                <!-- Mobile View Switcher Tabs -->
                <div class="flex border border-gray-200 dark:border-white/10 md:hidden rounded-xl p-1 bg-gray-100 dark:bg-black/20">
                    <button 
                        @click="showCartOnMobile = false" 
                        class="px-3 py-1 text-center text-xs font-bold transition rounded-lg" 
                        :class="!showCartOnMobile ? 'bg-[#D41414] text-white shadow' : 'text-gray-600 dark:text-gray-400'"
                    >
                        المنتجات
                    </button>
                    <button 
                        @click="showCartOnMobile = true" 
                        class="px-3 py-1 text-center text-xs font-bold transition rounded-lg flex items-center gap-1" 
                        :class="showCartOnMobile ? 'bg-[#D41414] text-white shadow' : 'text-gray-600 dark:text-gray-400'"
                    >
                        <span>السلة</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-white/20 text-white text-[10px]" x-text="cartCount()">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2-Column Responsive Workspace Grid -->
        <div class="pos-grid">
            
            <!-- Products Panel -->
            <div 
                class="pos-products-panel glass-panel p-4 overflow-hidden"
                :class="showCartOnMobile ? 'hidden md:flex' : 'flex'"
            >
                <!-- Search Bar & Categories Horizontal Bar -->
                <div class="space-y-3 mb-3 shrink-0">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input 
                            type="text" 
                            x-ref="searchInput"
                            placeholder="البحث بالاسم أو الباركود أو الـ IMEI (أو امسح الباركود مباشرة)..." 
                            class="w-full pr-10 pl-8 py-2.5 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl text-gray-900 dark:text-white text-xs placeholder-gray-400 focus:outline-none focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition font-medium"
                            x-model="searchQuery"
                            @keydown.enter.prevent="handleEnterKey()"
                        >
                        <button 
                            x-show="searchQuery" 
                            @click="searchQuery = ''; focusSearch();" 
                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-gray-900 dark:hover:text-white"
                        >
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                        <button 
                            @click="selectedCategory = ''"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 text-xs text-white"
                            :class="selectedCategory === '' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10'"
                        >
                            <i class="fa-solid fa-layer-group text-[11px]"></i>
                            <span>جميع الأقسام</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-black/10 dark:bg-white/20 font-bold" x-text="products.length"></span>
                        </button>

                        @foreach($categories as $cat)
                        <button 
                            @click="selectedCategory = '{{ $cat->id }}'"
                            class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 flex items-center gap-1.5 text-xs"
                            :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#D41414] text-white shadow-md glow-primary' : 'bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10'"
                        >
                            <span>{{ $cat->name }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-black/10 dark:bg-white/20 font-bold">{{ $cat->products_count }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="flex-1 overflow-y-auto grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pr-1 content-start">
                    <template x-for="prod in filteredProducts" :key="prod.id">
                        <div 
                            @click="addToCart(prod)"
                            class="group bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-white/10 hover:border-[#D41414]/50 p-3 rounded-xl flex flex-col justify-between cursor-pointer transition-all duration-150 hover:-translate-y-0.5 shadow-sm min-h-[115px]"
                        >
                            <div>
                                <div class="flex items-center justify-between gap-1 mb-1.5">
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-gray-300 font-semibold truncate" x-text="prod.category ? prod.category.name : 'عام'"></span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold">متوفر</span>
                                </div>
                                <h3 class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-[#D41414] transition line-clamp-2 leading-snug" x-text="prod.name"></h3>
                            </div>

                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200 dark:border-white/5">
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="numberFormat(prod.selling_price) + ' ' + window.defaultCurrency"></span>
                                <span class="w-6 h-6 rounded-lg bg-[#D41414]/10 group-hover:bg-[#D41414] text-[#D41414] group-hover:text-white flex items-center justify-center text-xs transition font-bold">
                                    <i class="fa-solid fa-plus"></i>
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredProducts.length === 0">
                        <div class="col-span-full py-16 text-center text-gray-400 text-xs">
                            <i class="fa-solid fa-box-open text-4xl text-gray-300 dark:text-white/10 mb-2 block"></i>
                            <p>لا توجد منتجات مطابقة للبحث أو القسم المختار.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart Side Panel -->
            <div 
                class="pos-cart-panel glass-panel p-4 overflow-hidden"
                :class="showCartOnMobile ? 'flex' : 'hidden md:flex'"
            >
                <!-- Cart Header -->
                <div class="border-b border-gray-200 dark:border-white/10 pb-3 mb-3 flex justify-between items-center shrink-0">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-[#D41414]"></i>
                        <span>سلة المشتريات</span>
                    </h2>
                    
                    <div class="flex items-center gap-2">
                        <button 
                            @click="clearCart()" 
                            x-show="cart.length > 0"
                            class="text-[10px] text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 font-bold px-2 py-0.5 rounded bg-rose-500/10 transition"
                            title="تفريغ السلة"
                        >
                            تفريغ
                        </button>
                        <span class="text-[10px] bg-[#D41414]/10 border border-[#D41414]/30 text-[#D41414] font-bold px-2.5 py-0.5 rounded-full" x-text="cartCount() + ' قطع'">0 قطع</span>
                    </div>
                </div>

                <!-- Customer Dropdown Selector & Quick Add Button -->
                <div class="mb-3 space-y-1 shrink-0">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">العميل والمشتري:</label>
                        <button 
                            type="button"
                            @click="openQuickCustomerModal()"
                            class="text-[10px] text-[#D41414] hover:text-[#A30F0F] font-bold flex items-center gap-1 transition"
                            title="تسجيل عميل جديد فوراً"
                        >
                            <i class="fa-solid fa-user-plus text-[9px]"></i>
                            <span>+ إضافة عميل سريع</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <select 
                            x-model="selectedCustomer"
                            class="flex-1 bg-gray-50 dark:bg-black/20 border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-xs rounded-xl px-2.5 py-2 focus:border-[#D41414] font-medium"
                        >
                            <option value="">عميل نقدي عام</option>
                            <template x-for="cust in customerList" :key="cust.id">
                                <option :value="cust.id" x-text="cust.name + ' (' + (cust.phone || 'بدون هاتف') + ')'" :selected="cust.id == selectedCustomer"></option>
                            </template>
                        </select>
                        <button 
                            type="button" 
                            @click="openQuickCustomerModal()"
                            class="p-2.5 bg-[#D41414] hover:bg-[#A30F0F] text-white rounded-xl text-xs transition shrink-0 shadow-sm flex items-center justify-center"
                            title="إضافة عميل جديد"
                        >
                            <i class="fa-solid fa-user-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5 mb-3 pr-1">
                    <template x-if="cart.length === 0">
                        <div class="py-16 text-center text-xs text-gray-400 dark:text-gray-500">
                            <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-gray-300 dark:text-white/10 block"></i>
                            <p class="font-bold text-gray-600 dark:text-gray-300">السلة فارغة</p>
                            <p class="text-[10px] text-gray-400 mt-1">انقر على أي منتج لإضافته فوراً</p>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="index">
                        <div class="py-2.5 flex justify-between items-center gap-2">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="item.name"></h4>
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold mt-0.5" x-text="numberFormat(item.selling_price) + ' ' + window.defaultCurrency"></p>
                            </div>
                            <div class="flex items-center space-x-1.5 space-x-reverse shrink-0">
                                <!-- Quantity Buttons -->
                                <button @click="decreaseQty(index)" class="w-6 h-6 rounded-lg bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/20 flex items-center justify-center text-gray-800 dark:text-white font-bold text-xs shadow-sm">-</button>
                                <span class="text-xs font-bold w-6 text-center text-gray-900 dark:text-white font-mono" x-text="item.qty">1</span>
                                <button @click="increaseQty(index)" class="w-6 h-6 rounded-lg bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/20 flex items-center justify-center text-gray-800 dark:text-white font-bold text-xs shadow-sm">+</button>
                                
                                <!-- Delete Item -->
                                <button @click="removeFromCart(index)" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 p-1 mr-1">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Financial Calculation Card -->
                <div class="border-t border-gray-200 dark:border-white/10 pt-3 space-y-2 shrink-0 bg-gray-50 dark:bg-black/20 p-3 rounded-xl border border-gray-200 dark:border-white/5">
                    <div class="flex justify-between text-xs text-gray-700 dark:text-gray-300">
                        <span>المجموع الفرعي:</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white" x-text="numberFormat(subtotal()) + ' ' + window.defaultCurrency">0.00</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-700 dark:text-gray-300">
                        <span>الضريبة (14% VAT):</span>
                        <span class="font-mono font-bold text-amber-600 dark:text-amber-400" x-text="numberFormat(tax()) + ' ' + window.defaultCurrency">0.00</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white border-t border-gray-200 dark:border-white/10 pt-2">
                        <span>الإجمالي النهائي:</span>
                        <span class="text-[#D41414] font-mono font-black text-base" x-text="numberFormat(total()) + ' ' + window.defaultCurrency">0.00</span>
                    </div>
                </div>

                <!-- Print Receipt Control Checkbox (Default: Off) -->
                <div class="pt-2">
                    <label class="flex items-center justify-between cursor-pointer select-none px-3 py-2 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/5 text-xs text-gray-700 dark:text-gray-300 transition hover:border-[#D41414]/30">
                        <span class="flex items-center gap-1.5 font-bold text-[11px]">
                            <i class="fa-solid fa-print text-amber-500"></i>
                            <span>طباعة الفاتورة بعد إتمام البيع</span>
                        </span>
                        <input type="checkbox" x-model="printReceiptOnCheckout" class="rounded text-[#D41414] focus:ring-[#D41414] w-4 h-4 cursor-pointer">
                    </label>
                </div>

                <!-- Complete Sale Action Buttons Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <!-- Standard Checkout (F8) -->
                    <button 
                        @click="checkout(false)"
                        class="py-2.5 px-3 bg-[#D41414] hover:bg-[#A30F0F] text-white font-bold rounded-xl transition-all glow-primary text-xs flex items-center justify-center gap-1.5 shrink-0 shadow-lg"
                        :disabled="cart.length === 0 || isCheckingOut"
                        :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        title="حفظ ودفع الفاتورة بدون طباعة"
                    >
                        <i class="fa-solid fa-check" x-show="!isCheckingOut"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isCheckingOut"></i>
                        <span>دفع وحفظ (F8)</span>
                    </button>

                    <!-- Checkout & Print (F9) -->
                    <button 
                        @click="checkout(true)"
                        class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all text-xs flex items-center justify-center gap-1.5 shrink-0 shadow-lg"
                        :disabled="cart.length === 0 || isCheckingOut"
                        :class="cart.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        title="حفظ ودفع وطباعة الفاتورة فوراً"
                    >
                        <i class="fa-solid fa-print" x-show="!isCheckingOut"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isCheckingOut"></i>
                        <span>دفع وطباعة 🖨️ (F9)</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Quick Add Customer Modal -->
    <div 
        x-show="showQuickCustomerModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
        @keydown.escape.window="showQuickCustomerModal = false"
    >
        <div 
            class="glass-panel w-full max-w-md p-5 space-y-4 rounded-2xl border border-white/10 shadow-2xl bg-[#121212] text-white relative"
            @click.away="showQuickCustomerModal = false"
        >
            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#D41414]"></i>
                    <span>إضافة عميل جديد سريعاً</span>
                </h3>
                <button type="button" @click="showQuickCustomerModal = false" class="text-gray-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="saveQuickCustomer()" class="space-y-3">
                <!-- Name -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-300">اسم العميل <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        x-ref="quickCustName"
                        x-model="newCustomer.name"
                        placeholder="مثال: أحمد محمد علي" 
                        required 
                        class="w-full px-3 py-2 bg-black/30 border border-white/15 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-300">رقم الهاتف <span class="text-rose-500">*</span></label>
                    <input 
                        type="tel" 
                        x-model="newCustomer.phone"
                        placeholder="010XXXXXXXX أو 011XXXXXXXX" 
                        required 
                        class="w-full px-3 py-2 bg-black/30 border border-white/15 rounded-xl text-white text-xs font-mono text-left focus:outline-none focus:border-[#D41414]"
                    >
                </div>

                <!-- Address / Notes -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-400">العنوان / ملاحظات (اختياري)</label>
                    <input 
                        type="text" 
                        x-model="newCustomer.address"
                        placeholder="مثال: وسط البلد - القاهرة" 
                        class="w-full px-3 py-2 bg-black/30 border border-white/15 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414]"
                    >
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                    <button 
                        type="button" 
                        @click="showQuickCustomerModal = false"
                        class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold rounded-xl transition"
                    >
                        إلغاء
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isSavingCustomer"
                        class="px-5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5 glow-primary"
                    >
                        <i class="fa-solid fa-check" x-show="!isSavingCustomer"></i>
                        <i class="fa-solid fa-spinner fa-spin" x-show="isSavingCustomer"></i>
                        <span>حفظ واختيار العميل</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
