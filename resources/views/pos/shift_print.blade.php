<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير تقفيل وردية #{{ $shift->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 min-h-screen flex flex-col items-center justify-center">

    <!-- Action Buttons -->
    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="px-5 py-2 bg-red-600 text-white font-bold rounded-lg shadow hover:bg-red-700 transition flex items-center gap-2 text-sm">
            🖨️ طباعة التقرير
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white font-bold rounded-lg shadow hover:bg-gray-700 transition text-sm">
            إغلاق
        </button>
    </div>

    <!-- 80mm Receipt / Summary Container -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 w-full max-w-sm text-gray-900 text-xs leading-relaxed space-y-4">
        
        <!-- Header -->
        <div class="text-center border-b pb-3 border-gray-200">
            <h1 class="text-base font-black tracking-wide">{{ setting('store_name', '2M Mobile') }}</h1>
            <p class="text-[11px] text-gray-500 font-semibold">تقرير تقفيل وتصفية وردية الكاشير</p>
            <div class="mt-2 inline-block px-3 py-1 bg-gray-100 rounded-full font-mono text-[10px] font-bold text-gray-700">
                وردية #{{ $shift->id }} ({{ $shift->status === 'open' ? 'مفتوحة حالياً' : 'مغلقة' }})
            </div>
        </div>

        <!-- Info Grid -->
        <div class="space-y-1.5 font-semibold text-gray-700 text-[11px]">
            <div class="flex justify-between">
                <span class="text-gray-500">الكاشير:</span>
                <span class="font-bold text-gray-900">{{ $shift->user->name ?? 'غير محدد' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">الفرع:</span>
                <span class="font-bold text-gray-900">{{ $shift->branch->name ?? 'الرئيسي' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">وقت الفتح:</span>
                <span class="font-mono text-gray-800">{{ $shift->opening_time ? $shift->opening_time->format('Y-m-d h:i A') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">وقت الإغلاق:</span>
                <span class="font-mono text-gray-800">{{ $shift->closing_time ? $shift->closing_time->format('Y-m-d h:i A') : 'مفتوحة' }}</span>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="border-t border-b py-3 border-gray-200 space-y-2">
            <h2 class="font-black text-gray-900 text-xs mb-2">📊 الملخص المالي للوردية:</h2>

            <div class="flex justify-between text-gray-700">
                <span>المبلغ الافتتاحي بالدرج:</span>
                <span class="font-mono font-bold">{{ number_format($shift->opening_balance, 2) }} ج.م</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>مبيعات نقدي (كاش):</span>
                <span class="font-mono font-bold text-emerald-600">+ {{ number_format($cashSales, 2) }} ج.م</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>مبيعات شبكة / محفظة:</span>
                <span class="font-mono font-bold text-blue-600">+ {{ number_format($cardSales, 2) }} ج.م</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>مبيعات آجـل:</span>
                <span class="font-mono font-bold text-purple-600">+ {{ number_format($creditSales, 2) }} ج.م</span>
            </div>
            <div class="flex justify-between text-gray-700 pt-1 border-t border-dashed">
                <span class="font-bold">إجمالي المبيعات ({{ $salesCount }} فاتورة):</span>
                <span class="font-mono font-black text-gray-900">{{ number_format($totalSales, 2) }} ج.م</span>
            </div>
        </div>

        <!-- Cash Register Reconciliation -->
        <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
            <div class="flex justify-between font-bold text-gray-800">
                <span>الكاش المتوقع في الدرج:</span>
                <span class="font-mono text-gray-900">{{ number_format($shift->expected_balance, 2) }} ج.م</span>
            </div>
            <div class="flex justify-between font-bold text-gray-800">
                <span>الكاش الفعلي المستلم:</span>
                <span class="font-mono text-gray-900">{{ number_format($shift->actual_balance ?? $shift->expected_balance, 2) }} ج.م</span>
            </div>
            
            @php
                $diff = $shift->difference ?? 0;
            @endphp
            <div class="flex justify-between font-black text-xs pt-1 border-t border-gray-300">
                <span>فروق الوردية (عجز / زيادة):</span>
                @if($diff == 0)
                    <span class="text-emerald-600">متطابق (0.00 ج.م)</span>
                @elseif($diff > 0)
                    <span class="text-blue-600">زيادة (+{{ number_format($diff, 2) }} ج.م)</span>
                @else
                    <span class="text-red-600">عجز ({{ number_format($diff, 2) }} ج.م)</span>
                @endif
            </div>
        </div>

        @if($shift->notes)
            <div class="text-[10px] text-gray-600 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                <strong>ملاحظات:</strong> {{ $shift->notes }}
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center text-[10px] text-gray-400 border-t pt-3 space-y-1">
            <p>تم استخراج التقرير في: {{ now()->format('Y-m-d h:i A') }}</p>
            <p class="font-semibold text-gray-600">توقيع المستلم / الكاشير: ........................</p>
        </div>

    </div>

</body>
</html>
