<x-app-layout>
    <div class="glass-panel p-4 md:p-6 space-y-4" x-data="{ showImportModal: false }">
        <!-- Header & Action Buttons Bar -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-white/5 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-mobile-screen text-[#D41414]"></i>
                    <span>المنتجات والأجهزة</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">إدارة الهواتف والإكسسوارات وقطع الغيار المعرفة بالنظام مع إمكانية التصدير والاستيراد الفوري.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Add New Product -->
                <a href="{{ route('products.create') }}" class="px-3.5 py-2 bg-[#D41414] hover:bg-[#A30F0F] text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>إضافة منتج جديد</span>
                </a>

                <!-- Import CSV Button -->
                <button 
                    @click="showImportModal = true" 
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-file-import text-xs"></i>
                    <span>استيراد منتجات (CSV)</span>
                </button>

                <!-- Export CSV Button -->
                <a href="{{ route('products.export') }}" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition border border-white/10 flex items-center gap-1.5" title="تصدير شيت كافة المنتجات">
                    <i class="fa-solid fa-file-export text-xs text-emerald-400"></i>
                    <span>تصدير الكل</span>
                </a>

                <!-- Download Sample Template -->
                <a href="{{ route('products.import-template') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white text-xs font-semibold rounded-xl transition border border-white/10 flex items-center gap-1.5" title="تحميل نموذج جاهز للاستيراد">
                    <i class="fa-solid fa-download text-xs text-amber-400"></i>
                    <span>تنزيل النموذج</span>
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-black/20 p-3 rounded-2xl border border-white/5">
            <!-- Search Box -->
            <div class="sm:col-span-2 relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    placeholder="ابحث باسم المنتج، رقم الباركود، أو الـ SKU..." 
                    value="{{ request('search') }}"
                    class="w-full pr-9 pl-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
            </div>

            <!-- Category Filter -->
            <div class="flex items-center gap-2">
                <select 
                    name="category_id" 
                    onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-[#0a0a0a] border border-white/10 rounded-xl text-white text-xs focus:outline-none focus:border-[#D41414]"
                >
                    <option value="">كافة التصنيفات والأقسام</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                @if(request('search') || request('category_id'))
                    <a href="{{ route('products.index') }}" class="px-3 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-400 text-xs font-bold rounded-xl transition flex items-center shrink-0" title="إلغاء الفلتر">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Products List Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-gray-400 border-b border-white/5 pb-2">
                        <th class="pb-2 px-3">الرمز (SKU)</th>
                        <th class="pb-2 px-3">المنتج / القسم</th>
                        <th class="pb-2 px-3">الباركود</th>
                        <th class="pb-2 px-3">سعر الشراء</th>
                        <th class="pb-2 px-3">سعر البيع</th>
                        <th class="pb-2 px-3">الكمية بالمخزن</th>
                        <th class="pb-2 px-3">تتبع السيريال</th>
                        <th class="pb-2 px-3 text-center">الخيارات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($products as $prod)
                        @php
                            $qty = $prod->inventories->sum('quantity');
                        @endphp
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3 px-3 font-mono font-bold text-white">{{ $prod->sku }}</td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-white block">{{ $prod->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $prod->category->name ?? 'عام' }}</span>
                            </td>
                            <td class="py-3 px-3 font-mono text-gray-400">{{ $prod->barcode ?: '—' }}</td>
                            <td class="py-3 px-3 font-mono text-gray-300">{{ number_format($prod->cost_price, 2) }} ج.م</td>
                            <td class="py-3 px-3 font-mono font-bold text-emerald-400">{{ number_format($prod->selling_price, 2) }} ج.م</td>
                            <td class="py-3 px-3">
                                @if($qty <= 0)
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 font-mono">0 {{ $prod->unit }} (نفذت)</span>
                                @elseif($qty <= $prod->minimum_stock)
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-mono">{{ $qty }} {{ $prod->unit }} (حرج)</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-mono">{{ $qty }} {{ $prod->unit }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($prod->has_serials)
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-blue-500/10 border border-blue-500/20 text-blue-400">نعم (IMEI)</span>
                                @else
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-white/5 text-gray-400">لا (كميات)</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('products.edit', $prod->id) }}" class="p-1.5 bg-white/5 hover:bg-white/10 rounded-lg text-gray-300 transition" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if($prod->has_serials)
                                        <a href="{{ route('products.barcode', $prod->id) }}" class="p-1.5 bg-white/5 hover:bg-[#D41414]/10 rounded-lg text-[#D41414] transition" title="طباعة الباركود والسيريال">
                                            <i class="fa-solid fa-barcode"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">لا توجد منتجات مسجلة تطابق البحث أو التصفية.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Import Modal -->
        <div 
            x-show="showImportModal" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
            @keydown.escape.window="showImportModal = false"
        >
            <div 
                class="glass-panel w-full max-w-lg p-6 space-y-4 rounded-2xl border border-white/10 shadow-2xl relative"
                @click.away="showImportModal = false"
            >
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-import text-emerald-400"></i>
                        <span>استيراد منتجات دفعة واحدة (CSV)</span>
                    </h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="p-4 bg-white/5 rounded-xl border border-white/10 text-xs space-y-2 text-gray-300">
                        <p class="font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-amber-400"></i> تعليمات الاستيراد:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-[11px] text-gray-400">
                            <li>يرجى رفع ملف شيت بصيغة <strong class="text-white">.csv</strong> أو <strong class="text-white">.xlsx</strong>.</li>
                            <li>يتم المطابقة بواسطة الرمز الفريد <strong class="text-white">SKU</strong> للمنتج.</li>
                            <li>إذا كان الـ SKU موجوداً مسبقاً، سيتم تحديث بياناته وتحديث القسم.</li>
                        </ul>
                        <div class="pt-1">
                            <a href="{{ route('products.import-template') }}" class="text-[#D41414] hover:underline font-bold text-[11px] flex items-center gap-1">
                                <i class="fa-solid fa-download"></i> انقر هنا لتنزيل شيت النموذج المجهز جاهزًا للتعبيئة
                            </a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-300">اختر ملف الاستيراد (.csv / .xlsx)</label>
                        <input 
                            type="file" 
                            name="file" 
                            accept=".csv, .xlsx, .xls, .txt" 
                            required
                            class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#D41414] file:text-white hover:file:bg-[#A30F0F] cursor-pointer bg-black/20 border border-white/10 rounded-xl p-1"
                        >
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                        <button 
                            type="button" 
                            @click="showImportModal = false"
                            class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-bold rounded-xl transition"
                        >
                            إلغاء
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5"
                        >
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>بدء الاستيراد والمعالجة</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
