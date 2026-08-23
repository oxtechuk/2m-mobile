<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="glass-panel p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">عرض تفاصيل فاتورة المبيعات</span>
                <h2 class="text-lg font-mono font-black text-white flex items-center gap-2">
                    {{ $sale->invoice_number }}
                    @if($sale->status === 'completed')
                        <span class="px-2 py-0.5 text-[9px] rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">مكتملة</span>
                    @else
                        <span class="px-2 py-0.5 text-[9px] rounded bg-rose-500/10 border border-rose-500/20 text-rose-400">ملغاة</span>
                    @endif
                </h2>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="px-3.5 py-1.5 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                    <i class="fa-solid fa-print"></i> طباعة الفاتورة (80mm)
                </a>
                
                @if($sale->status !== 'cancelled')
                    @can('process-return')
                    <a href="{{ route('returns.create', ['sale_id' => $sale->id]) }}" class="px-3.5 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> إرجاع منتجات (مرتجع)
                    </a>

                    <form method="POST" action="{{ route('sales.void', $sale->id) }}" onsubmit="return confirm('تحذير! سيؤدي إلغاء الفاتورة بالكامل إلى إرجاع البضائع للمخزن وسحب القيمة المالية من الخزينة. هل أنت متأكد؟');">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-rectangle-xmark"></i> إلغاء الفاتورة بالكامل
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Client & Cashier info -->
            <div class="glass-panel p-4 space-y-2">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">بيانات العميل والكاشير</h3>
                <p class="text-xs text-gray-400">العميل: <span class="font-semibold text-white">{{ $sale->customer->name ?? 'عميل نقدي عام' }}</span></p>
                @if($sale->customer)
                    <p class="text-xs text-gray-400">رقم الهاتف: <span class="font-mono text-white">{{ $sale->customer->phone }}</span></p>
                @endif
                <p class="text-xs text-gray-400">الكاشير المصدر: <span class="text-white">{{ $sale->user->name }}</span></p>
            </div>

            <!-- Invoice properties -->
            <div class="glass-panel p-4 space-y-2">
                <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">تفاصيل الفاتورة</h3>
                <p class="text-xs text-gray-400">فرع الإصدار: <span class="text-white">{{ $sale->branch->name ?? 'الرئيسي' }}</span></p>
                <p class="text-xs text-gray-400">طريقة الدفع: <span class="text-white uppercase font-bold">{{ $sale->payment_method }}</span></p>
                <p class="text-xs text-gray-400">تاريخ المعاملة: <span class="text-white font-mono">{{ $sale->created_at->format('Y-m-d h:i A') }}</span></p>
            </div>
        </div>

        <!-- Sold Items Table -->
        <div class="glass-panel p-4 md:p-6 space-y-3">
            <h3 class="text-xs font-bold text-white border-b border-white/5 pb-2">العناصر والمنتجات المباعة</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-1">
                            <th class="pb-1">اسم المنتج</th>
                            <th class="pb-1">الكمية</th>
                            <th class="pb-1">سعر القطعة</th>
                            <th class="pb-1 text-left">المجموع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($sale->items as $item)
                            <tr>
                                <td class="py-2.5">
                                    <span class="font-semibold text-white block">{{ $item->product->name }}</span>
                                    <span class="text-[10px] text-gray-500 font-mono">SKU: {{ $item->product->sku }}</span>
                                </td>
                                <td class="py-2.5 font-mono">{{ $item->quantity }}</td>
                                <td class="py-2.5 font-mono">{{ number_format($item->unit_price, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                                <td class="py-2.5 font-bold font-mono text-white text-left">{{ number_format($item->total_price, 2) }} {{ setting('default_currency', 'ج.م') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Tallies -->
            <div class="border-t border-white/5 pt-3 space-y-1.5 max-w-xs mr-auto text-xs text-gray-400">
                <div class="flex justify-between">
                    <span>المجموع الفرعي:</span>
                    <span class="font-mono text-white">{{ number_format($sale->subtotal, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>الضريبة (14% VAT):</span>
                    <span class="font-mono text-white">{{ number_format($sale->tax, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>الخصم المباشر:</span>
                    <span class="font-mono text-rose-500">- {{ number_format($sale->discount, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-white border-t border-white/5 pt-2">
                    <span>الإجمالي النهائي:</span>
                    <span class="text-emerald-400 font-mono">{{ number_format($sale->total, 2) }} {{ setting('default_currency', 'ج.م') }}</span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>