<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Header & Action Buttons -->
        <div class="flex items-center justify-between glass-panel p-4 md:p-6 print:hidden">
            <div>
                <h1 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-indigo-400"></i>
                    <span>تفاصيل فاتورة الشراء رقم: <span class="font-mono text-indigo-400">{{ $purchase->invoice_number }}</span></span>
                </h1>
                <p class="text-xs text-gray-400 mt-1">تاريخ الفاتورة: {{ $purchase->purchase_date->format('Y-m-d H:i') }} - الفرع: {{ $purchase->branch->name ?? '' }}</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg">
                    <i class="fa-solid fa-print"></i>
                    <span>طباعة الفاتورة</span>
                </button>

                <a href="{{ route('purchases.index') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl text-xs font-bold transition">
                    رجوع للسجل <i class="fa-solid fa-arrow-left mr-1"></i>
                </a>
            </div>
        </div>

        <!-- Printable Invoice Container -->
        <div class="glass-panel p-6 md:p-8 space-y-6 bg-[#0a0a0a] border border-white/10 rounded-2xl print:bg-white print:text-black print:border-none print:shadow-none">
            
            <!-- Invoice Header -->
            <div class="flex justify-between items-start border-b border-white/10 print:border-black/10 pb-6">
                <div>
                    <h2 class="text-xl font-black text-white print:text-black">{{ setting('store_name', '2M Mobile') }}</h2>
                    <p class="text-xs text-gray-400 print:text-gray-600 mt-1">{{ setting('store_address', 'العنوان الرئيسي للمؤسسة') }}</p>
                    <p class="text-xs text-gray-400 print:text-gray-600">هاتف: {{ setting('store_phone', '-') }}</p>
                </div>

                <div class="text-left font-mono">
                    <span class="text-xs text-gray-400 print:text-gray-600 block">فاتورة توريد مشتريات</span>
                    <span class="text-lg font-bold text-indigo-400 print:text-black block">{{ $purchase->invoice_number }}</span>
                    <span class="text-xs text-gray-500 block">{{ $purchase->purchase_date->format('Y/m/d H:i A') }}</span>
                </div>
            </div>

            <!-- Supplier & Branch Info -->
            <div class="grid grid-cols-2 gap-6 bg-white/5 print:bg-gray-100 p-4 rounded-xl">
                <div>
                    <span class="text-[10px] text-gray-400 print:text-gray-600 block font-bold">بيانات المورد:</span>
                    <span class="text-sm font-bold text-white print:text-black block mt-0.5">{{ $purchase->supplier->name }}</span>
                    @if($purchase->supplier->company_name)
                        <span class="text-xs text-gray-400 print:text-gray-700 block">{{ $purchase->supplier->company_name }}</span>
                    @endif
                    <span class="text-xs text-gray-400 print:text-gray-700 block font-mono">هاتف: {{ $purchase->supplier->phone ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-[10px] text-gray-400 print:text-gray-600 block font-bold">بيانات الفرع والمستلم:</span>
                    <span class="text-sm font-bold text-white print:text-black block mt-0.5">الفرع: {{ $purchase->branch->name ?? '' }}</span>
                    <span class="text-xs text-gray-400 print:text-gray-700 block">المستخدم المنشئ: {{ $purchase->creator->name ?? '' }}</span>
                    <span class="text-xs text-gray-400 print:text-gray-700 block">طريقة الدفع: {{ $purchase->payment_method }}</span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#121212] print:bg-gray-200 text-gray-300 print:text-black font-bold">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">اسم المنتج / الصنف</th>
                            <th class="p-3 text-center">الكمية</th>
                            <th class="p-3">تكلفة القطعة</th>
                            <th class="p-3">الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 print:divide-gray-300">
                        @foreach($purchase->items as $idx => $item)
                            <tr>
                                <td class="p-3 font-mono text-gray-400">{{ $idx + 1 }}</td>
                                <td class="p-3">
                                    <span class="font-bold text-white print:text-black block">{{ $item->product->name }}</span>
                                    @if(!empty($item->serials))
                                        <div class="mt-1 text-[10px] text-amber-400 print:text-gray-700 font-mono">
                                            <span>S/N / IMEIs: </span>
                                            <span>{{ implode(', ', $item->serials) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 text-center font-mono font-bold text-white print:text-black">{{ $item->quantity }}</td>
                                <td class="p-3 font-mono text-white print:text-black">{{ number_format($item->unit_cost, 2) }} ج.م</td>
                                <td class="p-3 font-mono font-bold text-white print:text-black">{{ number_format($item->subtotal, 2) }} ج.م</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Totals -->
            <div class="border-t border-white/10 print:border-black/10 pt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    @if($purchase->notes)
                        <span class="text-[10px] text-gray-400 print:text-gray-600 block">ملاحظات الفاتورة:</span>
                        <p class="text-xs text-gray-300 print:text-black italic mt-1">{{ $purchase->notes }}</p>
                    @endif
                </div>

                <div class="w-full md:w-72 space-y-2 font-mono text-xs">
                    <div class="flex justify-between items-center text-gray-400 print:text-gray-700">
                        <span>إجمالي الفاتورة:</span>
                        <span class="font-bold text-white print:text-black text-sm">{{ number_format($purchase->total_amount, 2) }} ج.م</span>
                    </div>

                    <div class="flex justify-between items-center text-emerald-400 print:text-emerald-700">
                        <span>المدفوع فوراً:</span>
                        <span class="font-bold">{{ number_format($purchase->paid_amount, 2) }} ج.م</span>
                    </div>

                    <div class="flex justify-between items-center text-rose-400 print:text-rose-700 border-t border-white/5 print:border-gray-300 pt-2">
                        <span>المتبقي آجل للمورد:</span>
                        <span class="font-bold text-sm">{{ number_format($purchase->due_amount, 2) }} ج.م</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
