<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Cairo:wght@400;600;700;900&display=swap');

        .barcode-studio-wrapper {
            font-family: 'Cairo', system-ui, sans-serif;
        }

        .barcode-mono-font {
            font-family: 'Share Tech Mono', monospace;
        }

        /* High contrast crisp rendering for thermal and optical barcode readers */
        svg.barcode-svg, svg.barcode-svg * {
            shape-rendering: crispEdges !important;
        }

        .qr-code-img {
            image-rendering: -webkit-optimize-contrast !important;
            image-rendering: crisp-edges !important;
            image-rendering: pixelated !important;
            display: block;
            margin: auto;
        }

        /* Interactive Barcode Label Box */
        .barcode-label {
            background: #ffffff !important;
            color: #000000 !important;
            border: 1px dashed #d1d5db;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            overflow: hidden;
            box-sizing: border-box;
            page-break-inside: avoid;
            break-inside: avoid;
            margin: auto;
            user-select: none;
            transition: all 0.15s ease;
        }

        /* QR Container sizing inside labels */
        .barcode-label .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1px;
            background: #ffffff !important;
        }
        .barcode-label .qr-container img, 
        .barcode-label .qr-container canvas {
            display: block;
            margin: auto;
            max-height: 48px;
            max-width: 48px;
        }

        /* Dimension Presets for Screen */
        .label-38x25 {
            width: 38mm;
            height: 25mm;
            padding: 1mm 1mm;
        }
        .label-38x25 .qr-container img,
        .label-38x25 .qr-container canvas {
            max-height: 38px;
            max-width: 38px;
        }

        .label-40x30 {
            width: 40mm;
            height: 30mm;
            padding: 1.5mm 1.5mm;
        }
        .label-40x30 .qr-container img,
        .label-40x30 .qr-container canvas {
            max-height: 48px;
            max-width: 48px;
        }

        .label-50x25 {
            width: 50mm;
            height: 25mm;
            padding: 1.5mm 1.5mm;
        }
        .label-50x30 {
            width: 50mm;
            height: 30mm;
            padding: 2mm 2mm;
        }
        .label-50x30 .qr-container img,
        .label-50x30 .qr-container canvas {
            max-height: 54px;
            max-width: 54px;
        }

        /* A4 Multi-Label Sheets */
        .label-a4-24 {
            width: 100%;
            max-width: 68mm;
            height: 34mm;
            padding: 2mm 2mm;
        }
        .label-a4-24 .qr-container img,
        .label-a4-24 .qr-container canvas {
            max-height: 56px;
            max-width: 56px;
        }

        .label-a4-30 {
            width: 100%;
            max-width: 68mm;
            height: 28mm;
            padding: 1.5mm 1.5mm;
        }
        .label-a4-30 .qr-container img,
        .label-a4-30 .qr-container canvas {
            max-height: 46px;
            max-width: 46px;
        }
    </style>

    <!-- JsBarcode Library for high quality linear barcodes -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <!-- QRCodeJS Library for QR code generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        window.barcodeStudioData = {
            initialProduct: @json($initialProduct ?? null),
            allProducts: @json($allProducts ?? []),
            testPrinterUrl: "{{ route('products.test-printer') }}",
            directPrintUrl: "{{ route('products.direct-print') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    <div class="barcode-studio-wrapper space-y-4" x-data="barcodeStudioApp()">
        
        <!-- Header & Quick Actions Bar (No-Print) -->
        <div class="glass-panel p-4 flex flex-wrap items-center justify-between gap-3 no-print">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#D41414]/10 border border-[#D41414]/20 flex items-center justify-center text-[#D41414] font-bold text-lg">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight">استوديو طباعة الباركود والـ QR Code</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">طابعة Xprinter الحرارية (38×25 مم) وطابعات الملصقات وشبكات أوراق A4.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Test Printer Connection Button -->
                <button 
                    type="button" 
                    @click="checkPrinterConnection()" 
                    class="px-3.5 py-2 rounded-xl bg-teal-500/15 border border-teal-500/30 text-teal-600 dark:text-teal-400 hover:bg-teal-500 hover:text-white text-xs font-bold transition flex items-center gap-2 shadow-sm"
                    :disabled="testingPrinter"
                >
                    <i class="fa-solid" :class="testingPrinter ? 'fa-spinner fa-spin text-teal-400' : 'fa-plug-circle-check text-sm text-teal-500'"></i>
                    <span x-text="testingPrinter ? 'جاري الفحص...' : 'فحص الاتصال'"></span>
                </button>

                <!-- 1-Click Instant TSPL Hardware Print (ZERO WASTE) -->
                <button 
                    type="button" 
                    @click="directHardwarePrint()" 
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition shadow-lg flex items-center gap-2"
                    :disabled="directPrinting"
                >
                    <i class="fa-solid" :class="directPrinting ? 'fa-spinner fa-spin' : 'fa-bolt'"></i>
                    <span x-text="directPrinting ? 'جاري الإرسال للطابعة...' : '⚡ طباعة فورية Xprinter (بدون هدر)'"></span>
                </button>
                
                <!-- Browser Print Window -->
                <button 
                    type="button" 
                    @click="printLabels()" 
                    class="px-5 py-2.5 rounded-xl bg-[#D41414] hover:bg-[#b01010] text-white text-xs font-black transition shadow-lg flex items-center gap-2"
                >
                    <i class="fa-solid fa-print text-sm"></i>
                    <span>🖨️ طباعة (نافذة المتصفح)</span>
                </button>
            </div>
        </div>

        <!-- Scanner Reliability Guarantee Banner -->
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 flex items-start gap-2.5 no-print">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base mt-0.5 shrink-0"></i>
            <div class="space-y-1">
                <p class="font-bold">✨ تم ضبط دقة الباركود مع هوامش أمان (Quiet Zones) لضمان القراءة الفورية عبر جميع أنواع أجهزة الاسكانر اليدوية وقارئات الموبايل بدون أي خطأ.</p>
                <p class="text-[11px] text-gray-600 dark:text-gray-400">
                    💡 <strong>نصيحة الطباعة من المتصفح:</strong> في نافذة الطباعة، تأكد من اختيار مقاس الورق (38×25 مم أو User Defined) وضبط الهوامش على <strong>None / بلا هوامش</strong>.
                </p>
            </div>
        </div>

        <!-- Printer Test Results Banner (Shown after test) -->
        <div 
            x-show="printerStatus.tested" 
            x-transition
            class="p-4 rounded-xl border flex flex-wrap items-center justify-between gap-3 no-print"
            :class="printerStatus.success ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-300'"
            style="display: none;"
        >
            <div class="flex items-center gap-3">
                <div 
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                    :class="printerStatus.success ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'"
                >
                    <i class="fa-solid" :class="printerStatus.success ? 'fa-circle-check' : 'fa-triangle-exclamation'"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold leading-tight" :class="printerStatus.success ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'" x-text="printerStatus.message"></h4>
                    <template x-if="printerStatus.success">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                            الطابعة: <strong class="text-gray-900 dark:text-white" x-text="printerStatus.printer"></strong> | المنفذ: <strong class="text-emerald-600 dark:text-emerald-400" x-text="printerStatus.port"></strong>
                        </p>
                    </template>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="checkPrinterConnection()" 
                    class="px-3 py-1.5 rounded-lg text-xs font-bold border transition bg-white/10 hover:bg-white/20 text-white"
                >
                    إعادة الفحص
                </button>
                <button 
                    type="button" 
                    @click="printerStatus.tested = false" 
                    class="text-gray-400 hover:text-white p-1"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Paper Preset Selector Quick Bar (Thermal vs A4 Buttons) -->
        <div class="glass-panel p-3 flex flex-wrap items-center justify-between gap-3 no-print">
            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                <i class="fa-solid fa-file-invoice text-[#D41414]"></i>
                <span>اختر نوع الورق والطباعة:</span>
            </div>

            <!-- Quick Buttons for Printers & Formats -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Xprinter 38x25 (Primary Recommended for Xprinter) -->
                <button 
                    type="button"
                    @click="setPreset('label-38x25')"
                    class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-1.5 border"
                    :class="labelPreset === 'label-38x25' ? 'bg-[#D41414] text-white border-[#D41414] shadow-lg ring-2 ring-red-400' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-200 dark:hover:bg-white/10'"
                >
                    <i class="fa-solid fa-receipt text-sm"></i>
                    <span>🏷️ رول حراري Xprinter (38×25 مم - المضبوط)</span>
                </button>

                <!-- Xprinter 50x25 -->
                <button 
                    type="button"
                    @click="setPreset('label-50x25')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                    :class="labelPreset === 'label-50x25' ? 'bg-[#D41414] text-white border-[#D41414] shadow' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-200 dark:hover:bg-white/10'"
                >
                    <i class="fa-solid fa-receipt"></i>
                    <span>رول حراري عريض (50×25 مم)</span>
                </button>

                <!-- A4 Sheet 24 Labels Button -->
                <button 
                    type="button"
                    @click="setPreset('label-a4-24')"
                    class="px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 border"
                    :class="labelPreset === 'label-a4-24' ? 'bg-indigo-600 text-white border-indigo-700 shadow-lg ring-2 ring-indigo-400' : 'bg-gray-100 dark:bg-white/5 text-indigo-600 dark:text-indigo-400 border-indigo-500/20 hover:bg-indigo-50 dark:hover:bg-indigo-950/40'"
                >
                    <i class="fa-solid fa-file-lines text-sm"></i>
                    <span>📄 ورقة A4 كاملة (24 ملصق)</span>
                </button>
            </div>
        </div>

        <!-- 2-Column Studio Grid (No-Print) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 no-print">
            
            <!-- Left 7 Cols: Products Selection & Custom QR Creator -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Mode Switch Tabs: Custom/External QR vs System Products -->
                <div class="glass-panel p-4 space-y-3">
                    
                    <!-- Tabs Header -->
                    <div class="flex border-b border-gray-200 dark:border-white/10 gap-2 pb-2">
                        <button 
                            type="button"
                            @click="activeTab = 'custom'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                            :class="activeTab === 'custom' ? 'bg-[#D41414] text-white shadow-md' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-white'"
                        >
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                            <span>إنشاء صنف خارجي مخصص / باركود / QR</span>
                        </button>
                        <button 
                            type="button"
                            @click="activeTab = 'system'" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                            :class="activeTab === 'system' ? 'bg-teal-700 text-white shadow-md' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-white'"
                        >
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span>اختيار من منتجات النظام المسجلة</span>
                        </button>
                    </div>

                    <!-- TAB 1: Custom / External QR Product Creator Form -->
                    <div x-show="activeTab === 'custom'" class="space-y-3 pt-1">
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/20 border border-amber-500/30 rounded-xl space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                                    <i class="fa-solid fa-barcode text-amber-500"></i>
                                    <span>كتابة بيانات صنف لتوليد باركود خطوط أو QR كود فوري:</span>
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <!-- Custom Name -->
                                <div class="sm:col-span-2">
                                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">اسم المنتج أو الوصف <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="text" 
                                        x-model="customItem.name"
                                        placeholder="مثال: شاحن شاومي 120W سريع" 
                                        class="w-full bg-white dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-white font-medium focus:border-amber-500"
                                    >
                                </div>

                                <!-- Custom Price -->
                                <div>
                                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">سعر البيع (ج.م) <span class="text-rose-500">*</span></label>
                                    <input 
                                        type="number" 
                                        step="0.5" 
                                        x-model.number="customItem.price"
                                        placeholder="650.00" 
                                        class="w-full bg-white dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 focus:border-amber-500"
                                    >
                                </div>

                                <!-- Code or QR Text -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block font-bold text-gray-700 dark:text-gray-300">رقم الكود / الباركود</label>
                                        <div class="flex gap-2">
                                            <button 
                                                type="button" 
                                                @click="customItem.code = '200' + Math.floor(Math.random() * 89999999 + 10000000);"
                                                class="text-[10px] text-teal-600 dark:text-teal-400 hover:underline font-mono"
                                            >
                                                أرقام (11 رقم)
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="customItem.code = '2M-' + Math.floor(Math.random() * 899999 + 100000);"
                                                class="text-[10px] text-amber-600 dark:text-amber-400 hover:underline font-mono"
                                            >
                                                كود 2M
                                            </button>
                                        </div>
                                    </div>
                                    <input 
                                        type="text" 
                                        x-model="customItem.code"
                                        placeholder="رقم باركود، كود صنف، أو رقم تسلسلي..." 
                                        class="w-full bg-white dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-xs font-mono text-gray-900 dark:text-white focus:border-amber-500"
                                    >
                                </div>

                                <!-- Symbol Type: Linear Barcode vs EAN13 vs QR Code -->
                                <div>
                                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">نوع الرمز على الملصق:</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button 
                                            type="button"
                                            @click="customItem.code_type = 'barcode'"
                                            class="py-1.5 px-2 rounded-lg border text-xs font-bold flex items-center justify-center gap-1.5 transition"
                                            :class="customItem.code_type === 'barcode' ? 'bg-amber-500 text-zinc-950 border-amber-600 font-black shadow' : 'bg-white dark:bg-zinc-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-zinc-700'"
                                        >
                                            <i class="fa-solid fa-barcode"></i>
                                            <span>باركود (خطوط - موصى به)</span>
                                        </button>
                                        <button 
                                            type="button"
                                            @click="customItem.code_type = 'qr'"
                                            class="py-1.5 px-2 rounded-lg border text-xs font-bold flex items-center justify-center gap-1.5 transition"
                                            :class="customItem.code_type === 'qr' ? 'bg-amber-500 text-zinc-950 border-amber-600 font-black shadow' : 'bg-white dark:bg-zinc-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-zinc-700'"
                                        >
                                            <i class="fa-solid fa-qrcode"></i>
                                            <span>QR Code (مربع)</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Quantity to Print -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block font-bold text-gray-700 dark:text-gray-300">عدد الملصقات <span class="text-rose-500">*</span></label>
                                        <div class="flex gap-1">
                                            <button type="button" @click="customItem.qty = 1" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 font-bold">1</button>
                                            <button type="button" @click="customItem.qty = 2" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 font-bold">2</button>
                                            <button type="button" @click="customItem.qty = 5" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 font-bold">5</button>
                                            <button type="button" @click="customItem.qty = 10" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-gray-300 font-bold">10</button>
                                        </div>
                                    </div>
                                    <input 
                                        type="number" 
                                        min="1" 
                                        step="1" 
                                        x-model.number="customItem.qty"
                                        placeholder="1" 
                                        class="w-full bg-white dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-xs font-mono font-bold text-gray-900 dark:text-white focus:border-amber-500 text-center"
                                    >
                                </div>
                            </div>

                            <div class="pt-2 border-t border-amber-200 dark:border-amber-800/40 flex justify-end">
                                <button 
                                    type="button"
                                    @click="addCustomProductToQueue()"
                                    class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-zinc-950 font-black text-xs transition shadow-md flex items-center gap-2"
                                >
                                    <i class="fa-solid fa-plus-circle"></i>
                                    <span>إضافة الملصق للمعاينة</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: System Products Search -->
                    <div x-show="activeTab === 'system'" class="space-y-3 pt-1" style="display: none;">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-[#D41414]"></i>
                                <span>البحث عن منتج مسجل في النظام:</span>
                            </label>
                            <button 
                                type="button" 
                                @click="addAllInStockProducts()" 
                                class="text-[11px] text-teal-600 dark:text-teal-400 hover:underline font-bold"
                            >
                                + إضافة كل المتوفر بالمخزن
                            </button>
                        </div>

                        <!-- Search Input with Dropdown -->
                        <div class="relative" @click.outside="showSearchDropdown = false">
                            <input 
                                type="text" 
                                placeholder="اكتب اسم المنتج أو الكود أو الباركود للاختيار..."
                                class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#D41414] focus:ring-1 focus:ring-[#D41414] transition font-medium"
                                x-model="productSearchQuery"
                                @input="filterProducts(); showSearchDropdown = true;"
                                @focus="filterProducts(); showSearchDropdown = true;"
                                @keydown.enter.prevent="selectFirstFiltered()"
                            >

                            <!-- Search Dropdown Results List -->
                            <div 
                                x-show="showSearchDropdown && filteredProductsList.length > 0"
                                class="absolute z-50 right-0 left-0 top-full mt-1 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-xl shadow-2xl max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-zinc-800"
                                style="display: none;"
                            >
                                <template x-for="p in filteredProductsList" :key="p.id">
                                    <div 
                                        @mousedown.prevent="addProductToQueue(p); showSearchDropdown = false; productSearchQuery = '';"
                                        class="p-2.5 hover:bg-teal-50 dark:hover:bg-teal-950/50 cursor-pointer transition flex items-center justify-between text-xs group"
                                    >
                                        <div class="min-w-0 flex-1 pr-2">
                                            <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 truncate" x-text="p.name"></h4>
                                            <div class="text-[11px] text-gray-500 font-mono flex gap-3 mt-0.5">
                                                <span>كود: <strong x-text="p.sku || p.barcode || '—'"></strong></span>
                                                <span>رصيد: <strong :class="p.stock_quantity > 0 ? 'text-teal-600 font-bold' : 'text-rose-500'" x-text="p.stock_quantity ?? 0"></strong></span>
                                            </div>
                                        </div>
                                        <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 shrink-0 text-left">
                                            <span x-text="numberFormat(p.selling_price) + ' ج.م'"></span>
                                            <span class="block text-[10px] text-teal-600 font-bold group-hover:underline">+ تحديد</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Direct Select Fallback Dropdown -->
                        <div class="flex items-center gap-2 pt-1">
                            <span class="text-[11px] text-gray-500 shrink-0">أو اختر من القائمة:</span>
                            <select 
                                class="flex-1 bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg px-2.5 py-1.5 text-xs text-gray-900 dark:text-white"
                                @change="if($event.target.value) { addProductById($event.target.value); $event.target.value = ''; }"
                            >
                                <option value="">-- اضغط لاختيار منتج مسجل --</option>
                                <template x-for="prod in allProducts" :key="'opt-' + prod.id">
                                    <option :value="prod.id" x-text="prod.name + ' - [' + (prod.barcode || prod.sku) + '] - (' + numberFormat(prod.selling_price) + ' ج.م)'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Products in Queue Table -->
                <div class="glass-panel p-4 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-teal-600"></i>
                            <span>قائمة الملصقات المحددة للطباعة (<span x-text="itemsQueue.length"></span> صنف)</span>
                        </h3>

                        <!-- Bulk Quantity Helpers -->
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <button 
                                type="button"
                                @click="fillQuantitiesFromStock()" 
                                class="px-2 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 font-bold text-[11px] hover:bg-teal-100 transition"
                            >
                                <i class="fa-solid fa-boxes-stacked ml-1"></i> رصيد المخزن
                            </button>
                            <button 
                                type="button"
                                @click="clearQueue()" 
                                class="text-rose-500 hover:text-rose-700 text-xs font-bold px-2 py-1"
                            >
                                تفريغ
                            </button>
                        </div>
                    </div>

                    <!-- Queue Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead>
                                <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/5 pb-2">
                                    <th class="py-2 pr-2">اسم الصنف / المنتج</th>
                                    <th class="py-2 text-center">نوع الرمز</th>
                                    <th class="py-2 text-center">السعر</th>
                                    <th class="py-2 text-center w-24">عدد الملصقات</th>
                                    <th class="py-2 text-center w-12">حذف</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                <template x-for="(item, index) in itemsQueue" :key="item.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                        
                                        <!-- Name -->
                                        <td class="py-2.5 pr-2">
                                            <div class="flex items-center gap-2">
                                                <span x-show="item.is_custom" class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 font-bold">مخصص</span>
                                                <p class="font-bold text-gray-900 dark:text-white truncate max-w-xs" x-text="item.name"></p>
                                            </div>
                                            <span class="text-[10px] text-gray-400 font-mono" x-text="'الكود: ' + (item.barcode || item.sku || '—')"></span>
                                        </td>

                                        <!-- Code Type Toggle (QR vs Barcode) -->
                                        <td class="py-2.5 text-center font-mono font-bold">
                                            <button 
                                                type="button" 
                                                @click="toggleItemCodeType(item)"
                                                class="px-2 py-1 rounded text-[11px] font-bold border transition flex items-center justify-center gap-1 mx-auto"
                                                :class="item.code_type === 'qr' ? 'bg-purple-500/15 border-purple-500/30 text-purple-600 dark:text-purple-400' : 'bg-teal-500/15 border-teal-500/30 text-teal-600 dark:text-teal-400'"
                                                title="اضغط للتبديل بين الباركود الخطي و QR Code"
                                            >
                                                <i class="fa-solid" :class="item.code_type === 'qr' ? 'fa-qrcode' : 'fa-barcode'"></i>
                                                <span x-text="item.code_type === 'qr' ? 'QR Code' : 'باركود'"></span>
                                            </button>
                                        </td>

                                        <!-- Selling Price -->
                                        <td class="py-2.5 text-center font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            <span x-text="numberFormat(item.selling_price) + ' ج.م'"></span>
                                        </td>

                                        <!-- Print Quantity Input -->
                                        <td class="py-2.5 text-center">
                                            <input 
                                                type="number" 
                                                min="1" 
                                                step="1" 
                                                x-model.number="item.print_qty"
                                                @input="renderAllBarcodes()"
                                                class="w-16 text-center bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-lg py-1 text-xs font-bold font-mono text-gray-900 dark:text-white focus:border-[#D41414]"
                                            >
                                        </td>

                                        <!-- Remove Item -->
                                        <td class="py-2.5 text-center">
                                            <button 
                                                type="button" 
                                                @click="removeItemFromQueue(index)" 
                                                class="text-rose-500 hover:text-rose-700 p-1"
                                                title="حذف من القائمة"
                                            >
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="itemsQueue.length === 0">
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                            <i class="fa-solid fa-qrcode text-3xl mb-2 text-gray-300 dark:text-zinc-700 block"></i>
                                            لم يتم إضافة أي ملصقات بعد. اكتب بيانات صنف في الأعلى أو اختر منتجاً لعرض الباركود.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right 5 Cols: Label Designer & Live Scanner Verification -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- Designer Settings Panel -->
                <div class="glass-panel p-4 space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-white/5">
                        <i class="fa-solid fa-sliders text-[#D41414]"></i>
                        <span>تخصيص مظهر وتصميم ملصق الباركود:</span>
                    </h3>

                    <!-- Label Size Presets -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">مقاس الورق والطباعة المختار:</label>
                        <select 
                            x-model="labelPreset" 
                            @change="renderAllBarcodes()"
                            class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-2 text-xs font-bold text-gray-900 dark:text-white focus:border-[#D41414]"
                        >
                            <option value="label-38x25">🏷️ 38mm × 25mm (رول باركود حراري Xprinter قياسي - المضبوط)</option>
                            <option value="label-40x30">🏷️ 40mm × 30mm (رول باركود حراري متوسط)</option>
                            <option value="label-50x25">🏷️ 50mm × 25mm (رول باركود حراري عريض)</option>
                            <option value="label-50x30">🏷️ 50mm × 30mm (رول باركود حراري للأجهزة)</option>
                            <option value="label-a4-24">📄 ورقة A4 مقسمة (24 ملصق بالصفحة - 3 أعمدة × 8 صفوف)</option>
                            <option value="label-a4-30">📄 ورقة A4 مقسمة (30 ملصق بالصفحة - 3 أعمدة × 10 صفوف)</option>
                        </select>
                    </div>

                    <!-- Display Content Checkboxes -->
                    <div class="space-y-2 pt-2 border-t border-gray-200 dark:border-white/5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">عناصر ومعلومات الملصق:</label>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <!-- Store Name -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showStoreName" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">اسم المتجر</span>
                            </label>

                            <!-- Product Name -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showProductName" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">اسم المنتج</span>
                            </label>

                            <!-- Selling Price -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showPrice" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">سعر البيع</span>
                            </label>

                            <!-- Barcode Number Text -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showBarcodeText" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">رقم الكود أسفل الخطوط</span>
                            </label>

                            <!-- SKU -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showSku" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">كود SKU</span>
                            </label>

                            <!-- Expiry Date -->
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/5">
                                <input type="checkbox" x-model="config.showExpiry" @change="renderAllBarcodes()" class="rounded text-[#D41414] focus:ring-[#D41414]">
                                <span class="font-medium text-gray-800 dark:text-gray-200 text-[11px]">تاريخ الصلاحية</span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Store Name Override -->
                    <div class="pt-2 border-t border-gray-200 dark:border-white/5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">اسم المتجر المطبوع على الملصق:</label>
                        <input 
                            type="text" 
                            x-model="config.storeName" 
                            @input="renderAllBarcodes()"
                            class="w-full bg-gray-50 dark:bg-[#0a0a0a] border border-gray-300 dark:border-white/10 rounded-xl px-3 py-1.5 text-xs text-gray-900 dark:text-white"
                        >
                    </div>

                </div>

                <!-- LIVE SCANNER TEST / VERIFICATION WIDGET -->
                <div class="glass-panel p-4 space-y-3 bg-gradient-to-br from-zinc-900 to-zinc-950 border border-teal-500/20">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-teal-400 flex items-center gap-2">
                            <i class="fa-solid fa-expand text-teal-400"></i>
                            <span>فحص وتجربة قارئ الباركود (Scanner Test)</span>
                        </h4>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-teal-500/10 text-teal-300 font-mono">جاهز للفحص</span>
                    </div>
                    <p class="text-[11px] text-gray-400">
                        وجّه جهاز الاسكانر أو كاميرا الموبايل نحو الشاشة أو الملصق المطبوع لاختبار القراءة الفورية:
                    </p>
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="scannerTestInput"
                            @keydown.enter="handleScannerTest()"
                            placeholder="اضغط هنا وقم بمسح الباركود بجهاز الاسكانر..."
                            class="w-full bg-black border border-teal-500/40 rounded-xl px-3 py-2 text-xs font-mono text-teal-300 placeholder-gray-500 focus:ring-1 focus:ring-teal-400 focus:border-teal-400"
                        >
                    </div>
                    <template x-if="lastScannedCode">
                        <div class="p-2 bg-teal-500/10 border border-teal-500/30 rounded-lg text-xs text-teal-300 flex items-center justify-between">
                            <span>✅ تمت القراءة بنجاح:</span>
                            <strong class="font-mono text-white" x-text="lastScannedCode"></strong>
                        </div>
                    </template>
                </div>

            </div>

        </div>

        <!-- 3. Live Interactive Preview Area -->
        <div class="glass-panel p-4 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-white/5 no-print">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-eye text-[#D41414]"></i>
                        <span>المعاينة الحية لصفحة الطباعة:</span>
                    </h3>
                    <span 
                        class="text-[11px] px-2.5 py-0.5 rounded-full font-black shadow-sm"
                        :class="isA4Preset() ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white'"
                        x-text="isA4Preset() ? '📄 وضع ورقة A4' : '🏷️ وضع رول Xprinter (38×25 مم)'"
                    ></span>
                </div>
                
                <span class="text-[11px] text-gray-500 font-mono">
                    إجمالي الملصقات: <strong class="text-emerald-600 dark:text-emerald-400 text-sm" x-text="totalLabelsCount() || customItem.qty || 1"></strong> ملصق
                </span>
            </div>

            <!-- Labels Container Grid -->
            <div 
                id="labels-render-area"
                class="print-container p-4 bg-gray-100 dark:bg-black/40 rounded-xl border border-gray-200 dark:border-white/5 min-h-[160px] overflow-x-auto"
                :class="isA4Preset() ? 'grid grid-cols-2 sm:grid-cols-3 gap-3 justify-center' : 'flex flex-wrap gap-3 justify-center'"
            >
                
                <template x-for="item in itemsQueue" :key="item.id">
                    <template x-for="copy in parseInt(item.print_qty || 1)" :key="item.id + '-' + copy">
                        <div 
                            class="barcode-label shadow-sm"
                            :class="labelPreset"
                        >
                            <!-- Store Name Header -->
                            <div 
                                x-show="config.showStoreName" 
                                class="text-[8px] font-black tracking-wider uppercase truncate w-full text-center" 
                                style="line-height: 1.1;"
                                x-text="config.storeName"
                            ></div>

                            <!-- Product Name -->
                            <div 
                                x-show="config.showProductName" 
                                class="text-[8.5px] font-bold truncate w-full px-0.5 text-center" 
                                style="line-height: 1.15;"
                                x-text="item.name"
                            ></div>

                            <!-- Middle Section: QR Code OR Barcode SVG -->
                            <template x-if="item.code_type === 'qr'">
                                <div class="my-0.5 qr-container" :id="'barcode-qr-' + item.id + '-' + copy"></div>
                            </template>
                            <template x-if="item.code_type !== 'qr'">
                                <div class="my-0.5 flex justify-center items-center w-full overflow-hidden">
                                    <svg :id="'barcode-svg-' + item.id + '-' + copy" class="barcode-svg max-w-full"></svg>
                                </div>
                            </template>

                            <!-- Bottom Row: Price & SKU / Expiry -->
                            <div class="w-full flex items-center justify-between px-1 text-[8px] font-bold" style="line-height: 1;">
                                <span x-show="config.showSku" class="font-mono opacity-80" x-text="item.sku || item.barcode || ''"></span>
                                <span x-show="config.showExpiry" class="font-mono opacity-80" x-text="item.expiry || ''"></span>
                                <span x-show="config.showPrice" class="font-mono font-black text-[9px] mr-auto" x-text="numberFormat(item.selling_price) + ' LE'"></span>
                            </div>
                        </div>
                    </template>
                </template>

                <template x-if="totalLabelsCount() === 0">
                    <div class="py-10 text-center text-gray-400 text-xs no-print col-span-full">
                        لا توجد ملصقات للمعاينة. أدخل بيانات صنف في الأعلى أو اختر منتجاً لعرض الباركود.
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- Alpine.js Barcode & QR Code Studio Logic -->
    <script>
        function barcodeStudioApp() {
            const data = window.barcodeStudioData || {};
            return {
                activeTab: 'custom',
                allProducts: data.allProducts || [],
                itemsQueue: [],
                productSearchQuery: '',
                filteredProductsList: [],
                showSearchDropdown: false,
                labelPreset: 'label-38x25', // Default to Xprinter 38x25mm
                testingPrinter: false,
                directPrinting: false,
                scannerTestInput: '',
                lastScannedCode: '',
                printerStatus: {
                    tested: false,
                    success: false,
                    printer: '',
                    port: '',
                    message: ''
                },
                customItem: {
                    name: '',
                    price: '',
                    code: '200' + Math.floor(Math.random() * 89999999 + 10000000),
                    code_type: 'barcode', // Default to linear barcode (lines)
                    qty: 1
                },
                config: {
                    storeName: "{{ setting('store_name', '2M Mobile') }}",
                    showStoreName: true,
                    showProductName: true,
                    showPrice: true,
                    showBarcodeText: true,
                    showSku: false,
                    showExpiry: false,
                    barcodeFormat: 'CODE128'
                },

                hashCode(str) {
                    let hash = 0;
                    for (let i = 0; i < str.length; i++) {
                        hash = ((hash << 5) - hash) + str.charCodeAt(i);
                        hash |= 0;
                    }
                    return Math.abs(hash) || 123456;
                },

                cleanBarcodeValue(item) {
                    let val = String(item.barcode || item.sku || '').trim();
                    // Remove quotes, formulas, non-printable chars
                    val = val.replace(/[\r\n\t="']/g, '').trim();
                    if (!val || !/^[\x20-\x7E]+$/.test(val)) {
                        val = '200' + String(Math.abs(this.hashCode(item.name || ('P' + item.id)))).padStart(8, '0').slice(0, 8);
                    }
                    return val;
                },

                handleScannerTest() {
                    const code = (this.scannerTestInput || '').trim();
                    if (code) {
                        this.lastScannedCode = code;
                        this.scannerTestInput = '';
                        // Play a pleasant scan beep
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.frequency.value = 1800;
                            gain.gain.value = 0.15;
                            osc.start();
                            setTimeout(() => { osc.stop(); ctx.close(); }, 80);
                        } catch(e) {}
                    }
                },

                renderAllBarcodes() {
                    if (typeof JsBarcode === 'undefined' || typeof QRCode === 'undefined') {
                        setTimeout(() => this.renderAllBarcodes(), 150);
                        return;
                    }

                    this.$nextTick(() => {
                        this.itemsQueue.forEach(item => {
                            const barcodeVal = this.cleanBarcodeValue(item);
                            const count = parseInt(item.print_qty || 1);
                            
                            for (let c = 1; c <= count; c++) {
                                const safeId = String(item.id).replace(/[^a-zA-Z0-9_-]/g, '_');
                                
                                // 1. If QR Code
                                if (item.code_type === 'qr') {
                                    const qrContainerId = `barcode-qr-${safeId}-${c}`;
                                    const qrEl = document.getElementById(qrContainerId);
                                    if (qrEl) {
                                        qrEl.innerHTML = '';
                                        try {
                                            const qrSize = this.isA4Preset() ? 52 : 38;
                                            new QRCode(qrEl, {
                                                text: barcodeVal,
                                                width: qrSize,
                                                height: qrSize,
                                                colorDark: "#000000",
                                                colorLight: "#ffffff",
                                                correctLevel: QRCode.CorrectLevel.M
                                            });

                                            // Convert canvas to crisp <img> for printing
                                            setTimeout(() => {
                                                const canvas = qrEl.querySelector('canvas');
                                                if (canvas) {
                                                    const dataUrl = canvas.toDataURL('image/png');
                                                    qrEl.innerHTML = `<img src="${dataUrl}" class="qr-code-img" style="width:${qrSize}px; height:${qrSize}px; display:block; margin:auto; image-rendering:pixelated; -webkit-print-color-adjust:exact;" alt="QR" />`;
                                                }
                                            }, 30);
                                        } catch(err) {
                                            console.error('QR Render Error:', err);
                                        }
                                    }
                                }
                                // 2. If Linear Barcode (Lines)
                                else {
                                    const svgElementId = `barcode-svg-${safeId}-${c}`;
                                    const svgEl = document.getElementById(svgElementId);
                                    if (svgEl) {
                                        try {
                                            const len = barcodeVal.length;
                                            let barWidth = 1.3;
                                            let barHeight = 32;
                                            let barMargin = 6;

                                            if (this.isA4Preset()) {
                                                barWidth = len > 14 ? 1.25 : 1.45;
                                                barHeight = 38;
                                                barMargin = 6;
                                            } else if (this.labelPreset === 'label-38x25') {
                                                // High-contrast, scannable Code128 dimensions for 38x25mm thermal label
                                                barWidth = len > 14 ? 1.05 : (len > 10 ? 1.2 : 1.35);
                                                barHeight = 30;
                                                barMargin = 5;
                                            } else if (this.labelPreset === 'label-50x25' || this.labelPreset === 'label-50x30') {
                                                barWidth = len > 14 ? 1.3 : 1.5;
                                                barHeight = 36;
                                                barMargin = 6;
                                            }

                                            // CODE128 supports all alphanumeric characters with full barcode scanner compatibility
                                            JsBarcode(svgEl, barcodeVal, {
                                                format: 'CODE128',
                                                width: barWidth,
                                                height: barHeight,
                                                displayValue: this.config.showBarcodeText,
                                                fontSize: 9,
                                                margin: barMargin,
                                                font: 'Share Tech Mono, monospace',
                                                textMargin: 1,
                                                lineColor: '#000000',
                                                background: '#ffffff',
                                                flat: true
                                            });

                                            svgEl.setAttribute('shape-rendering', 'crispEdges');
                                        } catch(err) {
                                            console.error('Barcode Render Error:', err);
                                            try {
                                                const fallbackCode = '200' + String(this.hashCode(barcodeVal)).padStart(8, '0').slice(0, 8);
                                                JsBarcode(svgEl, fallbackCode, {
                                                    format: 'CODE128',
                                                    width: 1.25,
                                                    height: 28,
                                                    displayValue: this.config.showBarcodeText,
                                                    fontSize: 9,
                                                    margin: 5,
                                                    font: 'Share Tech Mono, monospace',
                                                    lineColor: '#000000',
                                                    background: '#ffffff',
                                                    flat: true
                                                });
                                                svgEl.setAttribute('shape-rendering', 'crispEdges');
                                            } catch(e) {}
                                        }
                                    }
                                }
                            }
                        });
                    });
                },

                init() {
                    if (data.initialProduct) {
                        this.addProductToQueue(data.initialProduct, (data.initialProduct.stock_quantity > 0 ? data.initialProduct.stock_quantity : 1));
                        this.activeTab = 'system';
                    } else {
                        // Prepopulate with 1 sample item so preview and scanner are immediately visible and testable!
                        this.customItem.name = 'صنف تجريبي (شاحن سريع)';
                        this.customItem.price = 150;
                        this.customItem.code = '20042817291';
                        this.addCustomProductToQueue();
                    }
                    this.filteredProductsList = this.allProducts.slice(0, 10);
                    this.$nextTick(() => this.renderAllBarcodes());
                },
                        this.addProductToQueue(data.initialProduct, (data.initialProduct.stock_quantity > 0 ? data.initialProduct.stock_quantity : 1));
                        this.activeTab = 'system';
                    }
                    this.filteredProductsList = this.allProducts.slice(0, 10);
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                selectedPrinter: '',
                checkPrinterConnection() {
                    this.testingPrinter = true;
                    this.printerStatus.tested = false;

                    fetch(data.testPrinterUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': data.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            printer_name: this.selectedPrinter || ''
                        })
                    })
                    .then(res => res.json())
                    .then(resData => {
                        this.testingPrinter = false;
                        if (resData.printer) {
                            this.selectedPrinter = resData.printer;
                        }
                        this.printerStatus = {
                            tested: true,
                            success: resData.success,
                            printer: resData.printer || 'Xprinter XP-233B #2',
                            port: resData.port || 'USB003',
                            message: resData.message || (resData.success ? 'تم الاتصال بالطابعة وإرسال صفحة اختبار بنجاح' : 'تعذر الاتصال بالطابعة')
                        };
                    })
                    .catch(err => {
                        this.testingPrinter = false;
                        this.printerStatus = {
                            tested: true,
                            success: false,
                            printer: 'Xprinter XP-233B #2',
                            port: 'USB003',
                            message: 'حدث خطأ أثناء فحص اتصال الطابعة. يرجى التأكد من تشغيل الطابعة وتوصيل الكابل.'
                        };
                    });
                },

                async directHardwarePrint() {
                    // Auto queue custom item if name was entered
                    if (this.totalLabelsCount() === 0 && this.customItem.name.trim()) {
                        this.addCustomProductToQueue();
                    }

                    if (this.totalLabelsCount() === 0) {
                        alert('يرجى كتابة اسم صنف أو اختيار منتج أولاً لإضافته لقائمة الطباعة.');
                        return;
                    }

                    this.directPrinting = true;

                    // 1. Try Local Print Bridge on cashier machine (http://127.0.0.1:9191)
                    try {
                        const bridgeCheck = await fetch("http://127.0.0.1:9191/status", { method: 'GET', signal: AbortSignal.timeout(600) });
                        if (bridgeCheck.ok) {
                            const res = await fetch(data.directPrintUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': data.csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    items: this.itemsQueue,
                                    config: this.config,
                                    printer_name: this.selectedPrinter || ''
                                })
                            });
                            const resData = await res.json();
                            if (resData.tspl_data) {
                                const bridgeRes = await fetch("http://127.0.0.1:9191/print-raw", {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ data: resData.tspl_data, printer: this.selectedPrinter || '' })
                                });
                                if (bridgeRes.ok) {
                                    this.directPrinting = false;
                                    this.printerStatus = {
                                        tested: true,
                                        success: true,
                                        printer: 'Xprinter (Local Bridge)',
                                        port: 'USB',
                                        message: '✅ تمت الطباعة الفورية الصامتة بنجاح عبر الوسيط المحلي!'
                                    };
                                    return;
                                }
                            }
                        }
                    } catch(e) {}

                    // 2. Try backend direct print (works for local development)
                    try {
                        const res = await fetch(data.directPrintUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': data.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                items: this.itemsQueue,
                                config: this.config,
                                printer_name: this.selectedPrinter || ''
                            })
                        });

                        const resData = await res.json();
                        if (resData && resData.success) {
                            this.directPrinting = false;
                            this.printerStatus = {
                                tested: true,
                                success: true,
                                printer: resData.printer || this.selectedPrinter || 'Xprinter XP-233B #2',
                                port: resData.port || 'USB003',
                                message: '✅ ' + resData.message
                            };
                            return;
                        }
                    } catch (e) {
                        console.warn('Backend direct print not accessible on cloud server, falling back to client printing...', e);
                    }

                    // 3. On live remote server (e.g. cloud/VPS):
                    // Automatically open the isolated browser print dialog directly connected to local Xprinter!
                    this.directPrinting = false;
                    this.printLabels();
                },

                setPreset(presetName) {
                    this.labelPreset = presetName;
                    this.renderAllBarcodes();
                },

                isA4Preset() {
                    return this.labelPreset === 'label-a4-24' || this.labelPreset === 'label-a4-30';
                },

                addCustomProductToQueue() {
                    if (!this.customItem.name.trim()) {
                        alert('يرجى كتابة اسم المنتج أولاً.');
                        return;
                    }
                    const price = parseFloat(this.customItem.price) || 0;
                    const qty = parseInt(this.customItem.qty) || 1;
                    const code = this.customItem.code.trim() || ('200' + Math.floor(Math.random() * 89999999 + 10000000));

                    this.itemsQueue.push({
                        id: 'custom-' + Date.now() + Math.random().toString(36).substr(2, 4),
                        is_custom: true,
                        name: this.customItem.name.trim(),
                        sku: code,
                        barcode: code,
                        selling_price: price,
                        code_type: this.customItem.code_type || 'barcode',
                        print_qty: qty
                    });

                    // Reset form fields with new random code
                    this.customItem.name = '';
                    this.customItem.price = '';
                    this.customItem.code = '200' + Math.floor(Math.random() * 89999999 + 10000000);
                    this.customItem.qty = 1;

                    this.$nextTick(() => this.renderAllBarcodes());
                },

                toggleItemCodeType(item) {
                    item.code_type = (item.code_type === 'qr') ? 'barcode' : 'qr';
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                filterProducts() {
                    const q = (this.productSearchQuery || '').toLowerCase().trim();
                    if (!q) {
                        this.filteredProductsList = this.allProducts.slice(0, 10);
                        return;
                    }
                    this.filteredProductsList = this.allProducts.filter(p => 
                        (p.name && p.name.toLowerCase().includes(q)) ||
                        (p.sku && p.sku.toLowerCase().includes(q)) ||
                        (p.barcode && p.barcode.toLowerCase().includes(q))
                    ).slice(0, 15);
                },

                selectFirstFiltered() {
                    if (this.filteredProductsList.length > 0) {
                        this.addProductToQueue(this.filteredProductsList[0]);
                        this.showSearchDropdown = false;
                        this.productSearchQuery = '';
                    }
                },

                addProductById(id) {
                    const p = this.allProducts.find(item => item.id == id);
                    if (p) {
                        this.addProductToQueue(p);
                    }
                },

                addProductToQueue(prod, defaultQty = 1) {
                    if (!prod) return;
                    const found = this.itemsQueue.find(item => item.id === prod.id);
                    if (found) {
                        found.print_qty += 1;
                    } else {
                        this.itemsQueue.push({
                            id: prod.id,
                            is_custom: false,
                            name: prod.name,
                            sku: prod.sku,
                            barcode: prod.barcode || '',
                            code_type: 'barcode',
                            selling_price: parseFloat(prod.selling_price) || 0,
                            stock_quantity: prod.stock_quantity ?? 0,
                            print_qty: defaultQty
                        });
                    }
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                addAllInStockProducts() {
                    const inStock = this.allProducts.filter(p => (p.stock_quantity ?? 0) > 0);
                    if (inStock.length === 0) {
                        alert('لا توجد منتجات برصيد متاح في المخزن حالياً.');
                        return;
                    }
                    inStock.forEach(p => {
                        this.addProductToQueue(p, p.stock_quantity);
                    });
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                removeItemFromQueue(index) {
                    this.itemsQueue.splice(index, 1);
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                clearQueue() {
                    this.itemsQueue = [];
                },

                fillQuantitiesFromStock() {
                    this.itemsQueue.forEach(item => {
                        if (!item.is_custom) {
                            item.print_qty = item.stock_quantity > 0 ? item.stock_quantity : 1;
                        }
                    });
                    this.$nextTick(() => this.renderAllBarcodes());
                },

                totalLabelsCount() {
                    return this.itemsQueue.reduce((sum, item) => sum + (parseInt(item.print_qty) || 0), 0);
                },

                numberFormat(val) {
                    return (parseFloat(val) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                printLabels() {
                    // Auto queue custom item if name was entered
                    if (this.totalLabelsCount() === 0 && this.customItem.name.trim()) {
                        this.addCustomProductToQueue();
                    }

                    if (this.totalLabelsCount() === 0) {
                        alert('يرجى كتابة اسم صنف أو اختيار منتج أولاً لإضافته لقائمة الطباعة.');
                        return;
                    }

                    this.renderAllBarcodes();

                    // Print via isolated iframe with EXACT thermal label sizing & crisp contrast
                    this.$nextTick(() => {
                        setTimeout(() => {
                            const renderArea = document.getElementById('labels-render-area');
                            if (!renderArea) return;

                            // Ensure any remaining canvas inside render area is converted to <img>
                            const canvases = renderArea.querySelectorAll('canvas');
                            canvases.forEach(cv => {
                                try {
                                    const img = document.createElement('img');
                                    img.src = cv.toDataURL('image/png');
                                    img.className = 'qr-code-img';
                                    img.style.cssText = 'width: 36px; height: 36px; display: block; margin: auto; image-rendering: pixelated;';
                                    cv.parentNode.replaceChild(img, cv);
                                } catch(e) {}
                            });

                            const printHtml = renderArea.innerHTML;
                            const preset = this.labelPreset;
                            const isA4 = this.isA4Preset();

                            let printFrame = document.getElementById('barcode-print-iframe');
                            if (!printFrame) {
                                printFrame = document.createElement('iframe');
                                printFrame.id = 'barcode-print-iframe';
                                printFrame.style.position = 'fixed';
                                printFrame.style.right = '0';
                                printFrame.style.bottom = '0';
                                printFrame.style.width = '0';
                                printFrame.style.height = '0';
                                printFrame.style.border = 'none';
                                document.body.appendChild(printFrame);
                            }

                            // Dynamic Page size string based on preset
                            let pageSizeCss = 'size: 38mm 25mm landscape; margin: 0mm;';
                            let labelSizeCss = 'width: 38mm; height: 25mm; max-width: 38mm; max-height: 25mm; padding: 1mm 1mm;';

                            if (preset === 'label-50x25') {
                                pageSizeCss = 'size: 50mm 25mm landscape; margin: 0mm;';
                                labelSizeCss = 'width: 50mm; height: 25mm; max-width: 50mm; max-height: 25mm; padding: 1.5mm 1.5mm;';
                            } else if (preset === 'label-40x30') {
                                pageSizeCss = 'size: 40mm 30mm landscape; margin: 0mm;';
                                labelSizeCss = 'width: 40mm; height: 30mm; max-width: 40mm; max-height: 30mm; padding: 1.5mm 1.5mm;';
                            } else if (preset === 'label-50x30') {
                                pageSizeCss = 'size: 50mm 30mm landscape; margin: 0mm;';
                                labelSizeCss = 'width: 50mm; height: 30mm; max-width: 50mm; max-height: 30mm; padding: 1.5mm 1.5mm;';
                            }

                            const doc = printFrame.contentWindow.document;
                            doc.open();
                            doc.write(`
                                <!DOCTYPE html>
                                <html dir="rtl" lang="ar">
                                <head>
                                    <meta charset="utf-8">
                                    <title>طباعة الباركود - 2M Mobile</title>
                                    <style>
                                        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Cairo:wght@600;700;900&display=swap');
                                        * { 
                                            box-sizing: border-box; 
                                            margin: 0; 
                                            padding: 0; 
                                            -webkit-print-color-adjust: exact !important;
                                            print-color-adjust: exact !important;
                                            color-adjust: exact !important;
                                        }
                                        html, body { 
                                            font-family: 'Cairo', system-ui, sans-serif; 
                                            background: #ffffff !important; 
                                            color: #000000 !important; 
                                            margin: 0 !important; 
                                            padding: 0 !important; 
                                            overflow: hidden !important; 
                                        }
                                        
                                        svg, svg * {
                                            shape-rendering: crispEdges !important;
                                        }

                                        img, .qr-code-img {
                                            image-rendering: -webkit-optimize-contrast !important;
                                            image-rendering: crisp-edges !important;
                                            image-rendering: pixelated !important;
                                        }

                                        .barcode-label {
                                            background: #ffffff !important;
                                            color: #000000 !important;
                                            display: flex;
                                            flex-direction: column;
                                            align-items: center;
                                            justify-content: space-between;
                                            text-align: center;
                                            overflow: hidden;
                                            box-sizing: border-box;
                                        }
                                        .qr-container { display: flex; justify-content: center; align-items: center; }
                                        .qr-container img, .qr-container canvas { display: block; margin: auto; max-height: 36px; max-width: 36px; }

                                        ${isA4 ? `
                                            body { padding: 4mm 2mm; }
                                            .print-container {
                                                display: grid;
                                                grid-template-columns: repeat(3, 1fr);
                                                column-gap: 3mm;
                                                row-gap: 3mm;
                                                width: 100%;
                                                max-width: 195mm;
                                                margin: 0 auto;
                                            }
                                            .barcode-label {
                                                border: 0.5px solid #bbb;
                                                border-radius: 4px;
                                                page-break-inside: avoid;
                                                break-inside: avoid;
                                                width: 100%;
                                                height: 34mm;
                                                padding: 2mm 2mm;
                                            }
                                            @page { size: A4 portrait; margin: 6mm; }
                                        ` : `
                                            body { padding: 0; margin: 0; }
                                            .print-container {
                                                display: block;
                                                padding: 0;
                                                margin: 0;
                                            }
                                            .barcode-label {
                                                ${labelSizeCss}
                                                border: none !important;
                                                margin: 0 auto !important;
                                                page-break-after: always !important;
                                                break-after: page !important;
                                                page-break-inside: avoid !important;
                                                break-inside: avoid !important;
                                            }
                                            @page { ${pageSizeCss} }
                                        `}
                                    </style>
                                </head>
                                <body>
                                    <div class="print-container">
                                        ${printHtml}
                                    </div>
                                </body>
                                </html>
                            `);
                            doc.close();

                            setTimeout(() => {
                                printFrame.contentWindow.focus();
                                printFrame.contentWindow.print();
                            }, 300);
                        }, 250);
                    });
                }
            };
        }
    </script>
</x-app-layout>