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

        <!-- Products List Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-gray-400 border-b border-white/5 pb-2">
                        <th class="pb-2 px-3">الرمز (SKU)</th>
                        <th class="pb-2 px-3">اسم المنتج</th>
                        <th class="pb-2 px-3">سعر الشراء</th>
                        <th class="pb-2 px-3">سعر البيع</th>
                        <th class="pb-2 px-3">تتبع السيريال</th>
                        <th class="pb-2 px-3 text-center">الخيارات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @php
                        $products = \App\Models\Product::with('category')->latest()->get();
                    @endphp
                    @forelse($products as $prod)
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3 px-3 font-mono font-bold text-white">{{ $prod->sku }}</td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-white block">{{ $prod->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $prod->category->name ?? 'عام' }}</span>
                            </td>
                            <td class="py-3 px-3 font-mono text-gray-300">{{ number_format($prod->cost_price, 2) }} ج.م</td>
                            <td class="py-3 px-3 font-mono font-bold text-emerald-400">{{ number_format($prod->selling_price, 2) }} ج.م</td>
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
                                        <a href="{{ route('products.barcode', $prod->id) }}" class="p-1.5 bg-white/5 hover:bg-[#D41414]/10 rounded-lg text-[#D41414] transition" title="الباركود">
                                            <i class="fa-solid fa-barcode"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">لا توجد منتجات مسجلة حالياً. استخدم زر الاستيراد أو الإضافة للبدء.</td>
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
